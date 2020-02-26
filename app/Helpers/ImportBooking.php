<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use DateTime;

use App\Models\Booking;
use App\Models\BookingTicket;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\TourOption;
use App\Models\Tour;

class ImportBooking {

    public static function handle($sourceData)
    {
        $fetchedData = self::fetchBookingData($sourceData);

        if (!self::validateData($fetchedData)) {
            return false;
        }

        $booking = Booking::where('booking_number', '=', $fetchedData['booking_number'])->first();

        if ($booking) {
            return false;
        }

        $booking = self::createBooking($fetchedData);
        $bookingTickets = self::createTicket($booking, $sourceData);
        self::attachTicketOptions($booking, $fetchedData);
        self::calculatePrice($bookingTickets);

        return $booking;
    }

    public static function handleUpdate($sourceData)
    {
        $fetchedData = self::fetchBookingData($sourceData);

        if (!self::validateData($fetchedData)) {
            return false;
        }

        $booking = self::createBooking($fetchedData);

        BookingTicket::where('booking_id', '=', $booking->id)
            ->update(['quantity' => 0]);

        self::createTicket($booking, $sourceData);

        self::attachTicketOptions($booking, $fetchedData);
        self::calculatePrice($booking->bookingTickets);

        return $booking;
    }

    protected static function fetchBookingData($sourceItem) {
        $result = [
            'tour_bookeo_id' => $sourceItem['productId'] ?? $sourceItem['productId'],
            'booking_number' => $sourceItem['bookingNumber'] ?? $sourceItem['bookingNumber'],
            'title' => $sourceItem['productName'] ?? $sourceItem['productName'],
            'start_at' => self::fetchDate($sourceItem, 'startTime'),
            'end_at' => self::fetchDate($sourceItem, 'endTime'),
            'first_name' => Arr::get($sourceItem,'customer.firstName', null),
            'last_name' => Arr::get($sourceItem,'customer.lastName', null),
            'phone' => Arr::get($sourceItem, 'customer.phoneNumbers.0.number', null),
            'email' => Arr::get($sourceItem, 'customer.emailAddress', null),
            'country_code' => Arr::get($sourceItem, 'streetAddress.countryCode', null),
            'creation_agent' => Arr::get($sourceItem, 'creationAgent', null),

            // 'guid_id' => $sourceItem['bookingNumber'] ?? $sourceItem['bookingNumber'],
            // 'arrived_waiting_room' => $sourceItem['bookingNumber'] ?? $sourceItem['bookingNumber'],
            // 'source' => $sourceItem['bookingNumber'] ?? $sourceItem['bookingNumber'],
            'booking_options' => self::fetchOptions($sourceItem),
            'is_private_event' => $sourceItem['privateEvent'] ?? boolval($sourceItem['bookingNumber']),
            'participants' => Arr::get($sourceItem, 'participants', []),
            'source_data' => json_encode($sourceItem)
        ];

        return $result;
    }

    protected static function fetchDate($sourceItem, $fieldName)
    {
        $date = Arr::get($sourceItem, $fieldName, null);

        if (!$date) {
            return null;
        }

        return Carbon::createFromFormat(DateTime::ISO8601, $date)
            // ->setTimeZone(env('APP_TIME_ZONE'))
            ->format('Y-m-d H:i:s');
    }

    protected static function createBooking($data)
    {
        $bookeoId = '%' . $data['tour_bookeo_id'] . '%';
        $tour = Tour::where('bookeo_id', 'LIKE', $bookeoId)->first();

        $data['tour_id'] = $tour->id;


        $booking = Booking::updateOrCreate(
            ['booking_number' => $data['booking_number']],
            $data
        );

        return $booking;
    }

    protected static function createTicket($booking, $sourceItem)
    {
        $tickets = collect();

        if (empty($participants = Arr::get($sourceItem,'participants.numbers', []))) {
            return false;
        }

        foreach ($participants as $participant) {

            $ticket = Ticket::where('bookeo_type', '=', $participant['peopleCategoryId'])
                ->where('tour_id', '=', $booking->tour_id)
                ->first();

            $bookingTicket = BookingTicket::updateOrCreate(
                ['booking_id' => $booking->id, 'ticket_id' => $ticket->id],
                ['quantity' => $participant['number']]
            );

            $tickets->push($bookingTicket);
        }

        return $tickets;
    }

    protected static function calculatePrice($bookingTickets)
    {
        foreach ($bookingTickets as $bookingTicket) {
            $bookingTicket->calculatePrice();
        }
    }

    protected static function attachTicketOptions($booking, $sourceItem)
    {
        if (empty($participants = Arr::get($sourceItem,'participants.numbers', []))) {
            return false;
        }

        $typeList = Arr::pluck($participants, 'peopleCategoryId');
        $tourId   = $booking->tour_id;
        $idList   = [];

        $availableOptions = self::avaliableTourOptions($sourceItem);

        if ($availableOptions->count()) {
            $idList = $availableOptions->pluck('id')->toArray();
        }

        $tiketOptions = TicketOption::whereHas('ticket', function($query) use ($typeList, $tourId) {
                return $query->whereIn('bookeo_type', $typeList)
                    ->where('tickets.tour_id', '=', $tourId);
            })
            ->whereIn('tour_option_id', $idList)
            ->get();

        // $booking->ticketOptions()->syncWithoutDetaching($tiketOptions->pluck('id'));
        $booking->ticketOptions()->sync($tiketOptions->pluck('id'));
    }

    protected static function fetchOptions($sourceItem)
    {
        $selectedOptions = [];

        foreach (Arr::get($sourceItem, 'options', []) as $option) {
            if (empty($option['id'])) {
                continue;
            }

            if (empty($option['value']) || strtolower($option['value']) !== 'yes') {
                continue;
            }

            $selectedOptions[] = $option['id'];
        }

        return $selectedOptions;
    }

    protected static function validateData($data)
    {
        $bookeoId = '%' . $data['tour_bookeo_id'] . '%';
        $tour = Tour::where('bookeo_id', 'LIKE', $bookeoId)->first();

        if (!$tour) {
            return false;
        }

        return true;
    }

    protected static function avaliableTourOptions($sourceItem)
    {
        if (empty($sourceItem['booking_options'])) {
            return collect();
        }

        $query = TourOption::query();
        foreach ($sourceItem['booking_options'] as $identifier) {
            $query->orWhere('bookeo_id', 'LIKE', '%' . $identifier . '%');
        }

        return $query->get();
    }
}
