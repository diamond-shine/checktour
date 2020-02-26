<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Schedule;

class HandleBookings
{
    public static function onTourOptionUpdate($tourOption)
    {
        $forecasting = Booking::where(function($query) use ($tourOption) {
            $query->where('tour_id', '=', $tourOption->tour_id)
                ->where('start_at', '>=', Carbon::createMidnightDate())
                ->where('start_at', '<', Carbon::tomorrow())
                ->whereNull('schedule_id');
        })->get();

        $schedules = Schedule::where('tour_id', '=', $tourOption->tour_id)
            ->where('assigned_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('is_recruited', '=', 0);

        $rosteded = Booking::whereIn('schedule_id', $schedules->pluck('id'));

        self::handleBookings($forecasting);
        self::handleBookings($rosteded);
    }

    public static function onScheduleUpdate($schedule)
    {
        $bookings = Booking::where('schedule_id', '=', $schedule->id)->get();
        self::handleBookings($bookings);
    }

    public static function handleBooking(Booking $booking)
    {
        foreach($booking->bookingTickets as $bookingTicket) {
            $bookingTicket->calculatePrice();
        }
    }

    protected static function handleBookings($bookings)
    {
        $bookings->each(function($booking) {
            foreach($booking->bookingTickets as $bookingTicket) {
                $bookingTicket->calculatePrice();
            }
        });
    }

    public static function canProcess($booking)
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin() || $currentUser->hasTourConciergeRole()) {
            return true;
        }

        if ($booking->schedule && $booking->schedule->user_id != $currentUser->id) {
            return false;
        }

        return true;
    }
}
