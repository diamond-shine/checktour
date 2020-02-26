<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Tour;

use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;

use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Requests\Ticket\UpdateRequest;


use App\Http\Resources\Ticket as Resource;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TourCollection;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Ticket::query();

        if (is_valid_string($request->term)) {
            $query->where('name', 'like', "%{$request->term}%");
        }
        if ($request->tour_id) {
            $query->where('tour_id', '=', $request->tour_id);
        }

        $tours = $this->paginate($query, 9, $request->page);

        return response()->json([
                "data" => [
                    'items' => new TicketCollection($tours),
                    'pagination' => $this->mapPagination($tours),
                ],
                "meta" => [
                    Permissions::make('tickets', [
                        'tickets.create',
                        'tickets.view',
                        'tickets.edit',
                        'tickets.delete',
                    ])
                ]
        ]);
    }

    /**
     * @return array
     */
    public function create(): array
    {
        return [
            'data' => [
                'form' => [
                    'is_active' => false,
                ],
                'tours' => new TourCollection(Tour::query()->active()->get())
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
        $validatedData = $request->validated();

        $tour = Tour::where('id', '=', $validatedData['tour_id'])->firstOrfail();
        $ticket = new Ticket($validatedData);
        $ticket->save();
        return [
            'data' => [
                'form' => new Resource($ticket),
                'tours' => new TourCollection(Tour::query()->active()->get())
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  Tour  $tour
     * @return array
     */
    public function edit(Ticket $ticket)
    {
        $ticket->load('tour');
        return [
            'data' => [
                'form' => new Resource($ticket),
                'tours' => new TourCollection(Tour::query()->active()->get())
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        $ticket->load('tour');
        return [
            'data' => [
                'view' => new Resource($ticket)
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Ticket $ticket)
    {
        $validatedData = $request->validated();

        $tour = Tour::where('id', '=', $validatedData['tour_id'])->firstOrfail();

        $ticket->fill($validatedData);
        $ticket->save();
        $ticket->load('tour');

        return [
            'data' => [
                'view' => new Resource($ticket)
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return [
            'meta' => [
                Message::make(_('Ticket deleted'))->success(),
            ],
        ];
    }

    public function tour(Ticket $ticket)
    {
        $options = TourOption::get();
        return [
            'data' => $ticket->tour
        ];
    }
}
