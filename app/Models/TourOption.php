<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Tour;

class TourOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'bookeo_id', 'is_active', 'tour_id'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'bookeo_id' => 'array'
    ];


    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
