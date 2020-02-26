<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\BookingTicket;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;

class CreateBooking{
    public static function handle($sourceData)
    {
        $booking = Booking::where('booking_number', '=', $sourceData['booking_number'])->first();

        if ($booking) {
            return false;
        }

        $booking = self::createBooking($sourceData);
        $bookingTickets = self::createTicket($booking, $sourceData);

        self::attachTicketOptions($booking, $bookingTickets, $sourceData);
        self::calculate($bookingTickets);

        return $booking;
    }

    protected static function createBooking($sourceData)
    {
        $startTime = Carbon::now()->setTimeFromTimeString($sourceData['start_at']);

        $sourceData['title'] = _('Manually created');
        $sourceData['start_at'] = $startTime;
        $sourceData['end_at'] = $startTime->addMinutes(30);
        $sourceData['creation_agent'] = auth()->user()->id;
        $sourceData['source_data'] = json_encode($sourceData);
        $sourceData['arrived_waiting_room'] = true;

        $tour = Tour::where('id', '=', $sourceData['tour_id'])->first();
        $id = $tour->bookeo_id;
        $sourceData['tour_bookeo_id'] = reset($id);

        $booking = new Booking($sourceData);
        $booking->save();

        return $booking;
    }

    protected static function createTicket($booking, $sourceData)
    {
        $tickets = collect();
        $quantity = 0;

        throw_if(
            empty($participants = Arr::get($sourceData,'tickets_list', [])),
            'Empty tickets list'
        );

        foreach ($participants as $participant) {
            $quantity += $participant['quantity'];
            $ticket = Ticket::where('id', '=', $participant['ticket_id'])
                ->where('tour_id', '=', $booking->tour_id)
                ->first();

            $bookingTicket = BookingTicket::updateOrCreate(
                ['booking_id' => $booking->id, 'ticket_id' => $ticket->id],
                ['quantity' => $participant['quantity']]
            );

            $tickets->push($bookingTicket);
        }

        throw_if(empty($quantity), 'Empty participant');

        return $tickets;
    }

    protected static function attachTicketOptions($booking, $bookingTickets, $sourceData)
    {
        if (empty($optionsList = Arr::get($sourceData,'options_list', []))) {
            return false;
        }

        $tiketOptions = TicketOption::whereIn('tour_option_id', $optionsList)
            ->whereIn('ticket_id', $bookingTickets->pluck('ticket_id'));

        $booking->ticketOptions()->sync($tiketOptions->pluck('id'));
    }

    protected static function calculate($bookingTickets)
    {
        foreach ($bookingTickets as $bookingTicket) {
            $bookingTicket->calculatePrice();
        }
    }
}
