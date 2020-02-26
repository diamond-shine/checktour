<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ticket;
use Shelter\Guard\Models\User;
use App\Models\TourOption;
use App\Models\Excursion;
use App\Models\Schedule;

class Tour extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'currency',
        'bookeo_id',
        'is_active',
        'no_options_title'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'bookeo_id' => 'array'
    ];


    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tourOptions()
    {
        return $this->hasMany(TourOption::class);
    }

    public function excursions()
    {
        return $this->hasMany(Excursion::class);
    }

    public function schedules()
    {
        return $this->belongsTo(Schedule::class);
    }
}
