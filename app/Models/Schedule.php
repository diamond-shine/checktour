<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ideil\LaravelFileManager\Models\FileCollection;
use Ideil\LaravelFileManager\Traits\HasFilesTrait;
use App\Models\BookingTicket;
use App\Models\Excursion;
use App\Models\Tour;

use Actuallymab\LaravelComment\Contracts\Commentable;
use Actuallymab\LaravelComment\HasComments;

use Shelter\Guard\Models\User;
use Carbon\Carbon;

class Schedule extends Model implements Commentable
{
    use HasComments, HasFilesTrait;

    protected $fillable = [
        'tour_id',
        'user_id',
        'excursion_id',
        'is_enquired',
        'is_recruited',
        'is_finished',
        'assigned_at',
        'disabled_options'
    ];

    protected $casts = [
        'is_enquired' => 'bool',
        'is_recruited' => 'bool',
        'is_finished' => 'bool',
        'disabled_options' => 'array'
    ];

    /**
     * @return MorphToOne
     */
    public function images()
    {
        return $this->files();
    }


    public function excursion()
    {
        return $this->belongsTo(Excursion::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query->where('assigned_at', '=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeAvailableToday($query)
    {
        return $query->today()->whereHas('excursion', function ($query) {
            $query->where('time', '>=', Carbon::now()->sub('5 minutes')->format('H:i'));
        });
    }

    public function scopeTomorrow($query)
    {
        return $query->where('assigned_at', '=', Carbon::tomorrow()->format('Y-m-d'));
    }

    public function getTicketsSummary()
    {
        $scheduleId = $this->id;

        $bookingtickets = BookingTicket::whereHas('booking', function($query) use($scheduleId) {
            return $query->where('schedule_id', '=', $scheduleId);
        })->get();

        $bookingtickets->load('ticket');

        $grouped = [];
        $totalPrice = 0;
        $totalCount = 0;

        foreach($bookingtickets as $bookingTicket) {
            $totalPrice += $bookingTicket->getPrice();
            $totalCount += $bookingTicket->quantity;

            $options = $bookingTicket->activeTicketOptions();

            $key = $options->implode('tourOption.id', '_') . $bookingTicket->ticket->id;

            if (isset($grouped[$key])) {
                $grouped[$key]['quantity'] += $bookingTicket->quantity;
            } else {
                $grouped[$key] = [
                    'id' => $bookingTicket->ticket_id,
                    'name' => $bookingTicket->ticket->name,
                    'quantity' => $bookingTicket->quantity,
                    'option_name' => $options->implode('tourOption.name', ' '),
                ];
            }
        }

        return [
            'total_price' => $totalPrice,
            'total_count' => $totalCount,
            'by_types' => array_values($grouped)
        ];
    }

}
