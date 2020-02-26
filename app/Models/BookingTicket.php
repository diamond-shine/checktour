<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Ticket;

class BookingTicket extends Model
{
    protected $fillable = [
        'booking_id',
        'ticket_id',
        'quantity',
        'total_price',
        'total_price_modified'
    ];


    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticketOptions()
    {
        return $this->belongsToMany(
            TicketOption::class,
            'booking_ticket_option',
            'booking_id',
            'ticket_option_id',
            'booking_id' // field of this model
        )->where('ticket_options.ticket_id' , '=', $this->ticket_id);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getPrice()
    {
        if ($this->total_price != $this->total_price_modified) {
            return $this->total_price_modified;
        }

        return $this->total_price;
    }

    public function activeTicketOptions()
    {
        $schedule = $this->booking->schedule;
        $disabledOptions = $schedule ? $schedule->disabled_options : [];

        $ticketOptions = $this->ticketOptions->filter(function ($item) use ($disabledOptions) {
            if ($disabledOptions && in_array($item->tourOption->id, $disabledOptions)) {
                return false;
            }

            return $item->tourOption->is_active == 1;
        });

        return $ticketOptions;
    }

    public function calculatePrice()
    {
        $ticketOptions = $this->activeTicketOptions();

        $totalPrice = $ticketOptions->pluck('price')->sum() + $this->ticket->price;

        $this->total_price_modified = $totalPrice * $this->quantity;

        if (!$this->total_price) {
            $this->total_price = $this->total_price_modified;
        }

        $this->save();
    }
}
