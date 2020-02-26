<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

use App\Models\Tour;

class Excursion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tour_id', 'day', 'time'
    ];



    public function getDaytitle()
    {
        $daysList = [
            1  => _('Monday'),
            2  => _('Tuesday'),
            3  => _('Wednesday'),
            4  => _('Thursday'),
            5  => _('Friday'),
            6  => _('Saturday'),
            7  => _('Sunday')
        ];
        return $daysList[$this->day];
    }

    public function tour()
    {
        return belongsTo(Tour::class);
    }

    public static function getForecasted($excursions, $limit = 4) {

        $nowDateTme = Carbon::now();
        $dateTime = Carbon::now();

        $availableDates = [];

        for ($counter = 0; $counter < $limit; $counter++) {

            $validated = $excursions->filter(function ($excursion) use ($nowDateTme, $dateTime) {
                if ($dateTime->isoWeekday() == $excursion->day
                    && $dateTime->setTimeFromTimeString($excursion->time) > $nowDateTme) {
                    return true;
                }
            });

            if ($validated->count()) {
                foreach ($validated as $excursion) {
                    $prepared = $excursion->only('id', 'tour_id', 'time', 'day');
                    $prepared['day_title'] = $dateTime->format( 'l (d F)' );
                    $prepared['date_time'] = $dateTime->format('Y-m-d ') . ' ' . $excursion->time;
                    $availableDates[$prepared['day']][] = $prepared;
                }
            }

            $dateTime = $dateTime->add('1 day');
        }

        return array_values($availableDates);
    }
}
