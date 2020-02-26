<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Actuallymab\LaravelComment\CanComment;

use App\Models\Tour;
use App\Models\Schedule;

class User extends \Shelter\Guard\Models\User
{
    use CanComment;

    const ROLE_GUIDE = 'guide';
    const ROLE_TOUR_CONCIERGE = 'tour_concierge';

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class);
    }

    public function hasTourConciergeRole()
    {
        return !!$this->roles->first(function($role) {
            return $role->name == self::ROLE_TOUR_CONCIERGE;
        });
    }

    public function hasGuideRole()
    {
        return !!$this->roles->first(function($role) {
            return $role->name == self::ROLE_GUIDE;
        });
    }
}
