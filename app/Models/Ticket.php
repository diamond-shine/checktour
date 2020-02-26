<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Tour;
use App\Models\TicketOption;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'bookeo_type',  //peopleCategoryId on bookeo setvice
        'tour_id',
        'price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'bool'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function ticketOptions()
    {
        return $this->hasMany(TicketOption::class);
    }
}
