<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TourOption;
use App\Models\Ticket;

class TicketOption extends Model
{
    use SoftDeletes;

    protected $fillable = ['tour_option_id', 'ticket_id', 'price'];


    public function tourOption()
    {
        return $this->belongsTo(TourOption::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
