<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

use Actuallymab\LaravelComment\Contracts\Commentable;
use Actuallymab\LaravelComment\HasComments;

use App\Models\BookingTicket;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\Schedule;


class Booking extends Model implements Commentable
{
    use HasComments;
    use SoftDeletes;

    protected $fillable = [
        'tour_id',
        'booking_number',
        'tour_bookeo_id',
        'title',
        'start_at',
        'end_at',
        'first_name',
        'last_name',
        'phone',
        'email',
        'country_code',
        'schedule_id',
        'arrived_waiting_room',
        'source',
        'is_private_event',
        'creation_agent',
        'source_data'
    ];

    protected $casts = [
        'is_private_event' => 'bool',
        'arrived_waiting_room' => 'bool',
        'source_data' => 'array'
    ];

    /**
     * Alter Cast * Default is altering unicode
     * @param mixed $value
     */

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function ticketOptions()
    {
        return $this->belongsToMany(TicketOption::class);
    }

    public function bookingTickets()
    {
        return $this->hasMany(BookingTicket::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scopeInWaitingRoom($query)
    {
        return $query->where('arrived_waiting_room', '=', 1)
            ->where('updated_at', '>=', Carbon::createMidnightDate())
            ->where('updated_at', '<', Carbon::tomorrow())
            ->whereNull('schedule_id');
    }

    public function saveBookingTickets($itemsList)
    {
        $bookingTickets = collect();
        foreach ($itemsList as $item) {
            $ticket = Ticket::where('id', '=', $item['ticket_id'])->firstOrFail();

            $bookingTickets->push(
                BookingTicket::updateOrCreate(
                    ['booking_id' => $this->id, 'ticket_id' => $ticket->id],
                    ['quantity' => $item['quantity']]
                )
            );
        }

        $this->bookingTickets()
            ->whereNotIn('id', $bookingTickets->pluck('id'))
            ->delete();

        return $bookingTickets;
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
