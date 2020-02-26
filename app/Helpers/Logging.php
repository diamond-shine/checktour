<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Log;
use App\MOdels\Schedule;
use App\Http\Resources\Booking as BookingResource;
use App\Http\Resources\Roster as RosterResource;

class Logging
{
    public static function booking(Booking $booking, $snapshotBefore)
    {
        $currentUser = auth()->user();
        $data = [
            'user_id' => $currentUser->id,
            'entity_id' => $booking->id,
            'entity_type' => get_class($booking),
            'before' => $snapshotBefore,
            'after' => self::bookingSnapshot($booking),
        ];

        Log::create($data);
    }

    public static function roster(Schedule $roster, $snapshotBefore)
    {
        $currentUser = auth()->user();
        $data = [
            'user_id' => $currentUser->id,
            'entity_id' => $roster->id,
            'entity_type' => get_class($roster),
            'before' => $snapshotBefore,
            'after' => self::rosterSnapshot($roster),
        ];

        Log::create($data);
    }

    public static function bookingSnapshot(Booking $booking)
    {
        $resource = new BookingResource($booking);

        $resource = $resource->resolve();
        $resource['booking_tickets'] = $resource['booking_tickets']->resolve();
        $resource['ticket_options'] = $resource['ticket_options']->resolve();
        return $resource;
    }

    public static function rosterSnapshot(Schedule $roster)
    {
        $resource = new RosterResource($roster);
        $resource = $resource->resolve();

        return $resource;
    }
}
