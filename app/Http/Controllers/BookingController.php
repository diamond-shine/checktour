<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

use App\Helpers\CreateBooking;
use App\Helpers\HandleBookings;
use App\Helpers\Notification;
use App\Helpers\Logging;

use App\Models\Booking;
use App\Models\BookingTicket;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourOption;

use App\Http\Requests\Booking\Process as BookingProcessRequest;
use App\Http\Requests\Booking\Update as UpdateRequest;
use App\Http\Requests\Booking\StoreRequest as StoreRequest;

use App\Http\Resources\Booking as Resource;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\ScheduleCollection;
use App\Http\Resources\TourOptionCollection;
use App\Http\Resources\TicketCollection;

use App\Tools\Meta\Permissions;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected function bookingsList(Request $request, $query, $load = [])
    {
        if (is_valid_string($request->term)) {
            $query = $query->where(function($query) use($request) {
                $query->where('first_name', 'like', "%{$request->term}%")
                ->orWhere('last_name', 'like', "%{$request->term}%")
                ->orWhere('booking_number', 'like', "%{$request->term}%")
                ->orWhere('phone', 'like', "%{$request->term}%");

                if (filter_var($request->term, FILTER_VALIDATE_EMAIL )) {
                    $query = $query->orWhere('email', 'like', "%{$request->term}%");
                }
            });
        }

        if ($tourId = $request->get('tour_id', null)) {
            $query->where('tour_id', '=', $tourId);
        }

        if ($startTime = $request->get('start_time', null)) {
            $query->where('start_at', '>', Carbon::createFromTimeString($startTime));
        }

        if ($endTime = $request->get('end_time', null)) {
            $query->where('start_at', '<', Carbon::createFromTimeString($endTime));
        }

        $bookings = $this->paginate($query, 9, $request->page);

        $load[] = 'tour';

        $bookings->load($load);

        return response()->json([
                "data" => [
                    'items' => new BookingCollection($bookings),
                    'pagination' => $this->mapPagination($bookings),
                ],
                "meta" => [
                    Permissions::make('bookings', [
                        'bookings.create',
                        'bookings.view',
                        'bookings.edit',
                        'rosters.permit'
                    ])
                ]
        ]);
    }

    public function index(Request $request)
    {
        $query = Booking::query();
        $query->orderBy('updated_at', 'DESC');

        return $this->bookingsList($request, $query, [
            'schedule',
            'bookingTickets',
        ]);
    }

    public function listForecasting(Request $request)
    {
        $query = Booking::where(function($query) {
            $query->where('start_at', '>=', Carbon::createMidnightDate())
                ->where('start_at', '<', Carbon::tomorrow())
                ->whereNull('schedule_id');
        });

        $query->orderBy('start_at', 'DESC');

        return $this->bookingsList($request, $query);
    }

    public function listRostered(Request $request)
    {
        $schedules = Schedule::where('assigned_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('is_finished', '=', 0);

        $query = Booking::whereIn('schedule_id', $schedules->pluck('id'))
            ->orderBy('start_at', 'DESC');

        return $this->bookingsList($request, $query);
    }

    public function listProcessed(Request $request)
    {
        $query = Booking::where(function($query) {
            $query
                ->where('updated_at', '>=', Carbon::createMidnightDate())
                ->where('updated_at', '<', Carbon::tomorrow())
                ->whereHas('schedule', function($query) {
                    return $query->where('is_finished', '=', 1);
                });
        });

        return $this->bookingsList($request, $query, ['schedule']);
    }

    public function rosterBookings(Request $request, Schedule $roster)
    {
        $query = Booking::where('schedule_id', '=', $roster->id);

        $query->orderBy('start_at', 'DESC');

        return $this->bookingsList($request, $query);
    }

    public function listWaitingRoom(Request $request)
    {
        $query = Booking::where(function($query) {
            $query->inWaitingRoom();
        });

        $query->orderBy('start_at', 'DESC');

        return $this->bookingsList($request, $query);
    }

    public function toursAutocomplete() {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin() || $currentUser->hasTourConciergeRole()) {
            $tours = Tour::query()->active()->get();
        } else {
            $tours = $currentUser->tours;
        }

        return response()->json([
            "data" => [
                'items' => $tours->map->only('id', 'name'),
            ],
            "meta" => []
        ]);
    }


    public function autocomplete(Request $request)
    {
        $query = Schedule::query();

        if ($tourId = $request->get('tour_id', null)) {
            $query->where('tour_id', '=', $tourId);
        }


        $currentUser = auth()->user();

        if (!$currentUser->isAdmin() && !$currentUser->hasTourConciergeRole()) {
            $query->where('user_id', $currentUser->id);
        }

        $schedules = $query->availableToday()->get();
        $schedules->load('user', 'excursion');

        return response()->json([
            "data" => [
                'items' => new ScheduleCollection($schedules),
            ],
            "meta" => [
                Permissions::make('bookings', [
                    'bookings.create',
                    'bookings.view',
                    'bookings.edit',
                    'rosters.permit'
                ])
            ]
        ]);
    }

    /**
     * @param  BookingProcessRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function process(BookingProcessRequest $request, Booking $booking)
    {
        $validated = $request->validated();

        if (! HandleBookings::canProcess($booking)) {
            return response('Permission denied', 403);
        }

        DB::transaction(function() use ($validated, $booking) {
            $booking->fill($validated);
            $booking->save();

            if (!empty($validated['comment'])) {
                $currentUser = auth()->user();
                $currentUser->comment($booking, $validated['comment']);
            }

            HandleBookings::handleBooking($booking);

            Notification::booking($booking, _('Booking added to roster'));

        }, 2);

        $comments = $booking->comments()->orderBy('created_at', 'DESC')->get();
        $comments->load('commented');

        $booking->fresh();
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets',
            'bookingTickets.ticket',
            'ticketOptions.tourOption',
            'schedule',
            'schedule.user',
            'schedule.excursion'
        );

        return [
            'data' => [
                'view' => new Resource($booking),
                'comments' => new CommentCollection($comments)
            ]
        ];
    }

        /**
     * @return array
     */
    public function create(): array
    {
        $tour = Tour::first();
        $tour->load('tourOptions');

        $startTime = Carbon::now()
            ->addMinutes(15)
            ->ceilMinute(15)
            ->format('H:i');

        return [
            'data' => [
                'form' => [
                   'tour_id' => $tour->id,
                   'booking_number' => uniqid(),
                   'tour_bookeo_id' => $tour->bookeo_id,
                   'title' => _('Manually created'),
                   'start_at' => $startTime,
                   'country_code' => null,
                   'schedule_id' => null,
                   'arrived_waiting_room' => true,
                   'is_private_event' => false,
                   'tour' => $tour,
                   'ticket_options' => [],
                   'booking_tickets' => [],
                   'schedule' => null,
                   'start_total_price' => 0,
                   'total_price' => 0,
                   'tickets_count' => 0,
                   'currency' => 'EUR'
                ]
            ],
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $booking = null;
        DB::transaction(function () use ($validated, &$booking) {
            $booking = CreateBooking::handle($validated);

            if (!empty($validated['comment'])) {
                $currentUser = auth()->user();
                $currentUser->comment($booking, $validated['comment']);
            }
        }, 2);

        $booking->fresh();
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets',
            'bookingTickets.ticket',
            'ticketOptions.tourOption',
            'schedule',
            'schedule.user',
            'schedule.excursion'
        );

        $comments = $booking->comments()->orderBy('created_at', 'DESC')->get();
        $comments->load('commented');

        return [
            'data' => [
                'form' => new Resource($booking)
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets',
            'bookingTickets.ticket',
            'ticketOptions.tourOption',
            'schedule',
            'schedule.user',
            'schedule.excursion'
        );

        $comments = $booking->comments()->orderBy('created_at', 'DESC')->get();
        $comments->load('commented');

        return [
            'data' => [
                'view' => new Resource($booking),
                'comments' => new CommentCollection($comments)
            ]
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets',
            'bookingTickets.ticket',
            'ticketOptions.tourOption',
            'schedule',
            'schedule.user'
        );

        return [
            'data' => [
                'form' => new Resource($booking)
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Booking $booking)
    {
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets',
            'bookingTickets.ticket',
            // 'ticketOptions.tourOption',
            'schedule',
            'schedule.user',
            'schedule.excursion'
        );

        $validated = $request->validated();
        $beforeSnapshot = Logging::bookingSnapshot($booking);

        $this->isValidTicketsQuantity($booking, $validated['tickets_list']);

        $beforeTotalPrice = $booking->bookingTickets->sum(function ($bookingTicket) {
            return $bookingTicket->getPrice();
        });

        DB::transaction(function () use ($booking, $validated, $beforeTotalPrice) {
            $booking->fill($validated);
            $booking->save();

            $currentUser = auth()->user();

            if (!empty($validated['comment'])) {
                $currentUser->comment($booking, $validated['comment']);
            }

            $bookingTickets = $booking->saveBookingTickets($validated['tickets_list']);

            $ticketOptions = TicketOption::whereIn('ticket_id', $bookingTickets->pluck('ticket_id'))
                ->whereIn('tour_option_id', $validated['options_list'])->get();

            $booking->ticketOptions()->sync($ticketOptions->pluck('id'));

            foreach($bookingTickets as $item) {
                $item->calculatePrice();
            }

            $booking->load('bookingTickets');

            if (!$currentUser->isAdmin() || !$currentUser->hasTourConciergeRole()) {
                $totalPrice = $booking->bookingTickets->sum(function ($bookingTicket) {
                    return $bookingTicket->getPrice();
                });

                abort_if($beforeTotalPrice < $totalPrice, 422);
            }


        }, 2);

        $booking->fresh();
        $booking->load(
            'tour',
            'tour.tourOptions',
            'ticketOptions',
            'bookingTickets.ticket',
            'ticketOptions.tourOption',
            'schedule',
            'schedule.user',
            'schedule.excursion'
        );

        Logging::booking($booking, $beforeSnapshot);

        return [
            'data' => [
                'view' => new Resource($booking)
            ]
        ];
    }

    public function optionsAutocomplete(Tour $tour)
    {
        $options = TourOption::where('tour_id', $tour->id)->get();

        return response()->json([
            "data" => [
                'items' => new TourOptionCollection($options),
            ],
            "meta" => []
        ]);
    }

    public function ticketsAutocomplete(Tour $tour)
    {
        $tickets = $tour->tickets;
        $tickets->load('ticketOptions', 'ticketOptions.tourOption');

        return response()->json([
            "data" => [
                'items' => new TicketCollection($tickets),
            ],
            "meta" => []
        ]);
    }

    public function isValidTicketsQuantity($booking, $newTickets)
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin() || $currentUser->hasTourConciergeRole()) {
            return true;
        }

        foreach ($newTickets as $ticket) {
            $oldTicket = $booking->bookingTickets->firstWhere('ticket_id', $ticket['ticket_id']);
            abort_if($ticket['quantity'] && ($oldTicket->quantity < $ticket['quantity']), 422);
        }
    }
}
