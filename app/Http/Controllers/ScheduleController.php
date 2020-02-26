<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Resources\Schedule as Resource;
use App\Http\Resources\ScheduleCollection;
use App\Http\Resources\TourCollection;
use App\Http\Resources\User\ExtendListResource as UserCollection;

use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;

use App\Http\Requests\Schedule\{
    StoreRequest,
    UpdateRequest
};

use App\Models\Excursion;
use App\Models\Schedule;
use App\Models\Tour;
use Shelter\Guard\Models\User;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Schedule::query();

        if (is_valid_string($request->term)) {
            $query->where('name', 'like', "%{$request->term}%");
        }

        $schedules = $this->paginate($query, 9, $request->page);
        $schedules->load('tour', 'user');

        return response()->json([
                "data" => [
                    'items' => new ScheduleCollection($schedules),
                    'pagination' => $this->mapPagination($schedules),
                ],
                "meta" => [
                    Permissions::make('schedules', [
                        'schedules.create',
                        'schedules.view',
                        'schedules.edit',
                        'schedules.delete',
                    ])
                ]
        ]);
    }


    public function tours()
    {
        $tours = Tour::query()->active()->get();

        return response()->json([
                "data" => [
                    'items' => new TourCollection($tours),
                ],
                "meta" => []
        ]);
    }

    public function scheduleUsers(Request $request) {
        $tourId = $request->get('tour_id');

        $query = \App\Models\User::whereHas('tours', function ($query) use ($tourId) {
                return $query->where('tours.id', '=', $tourId);
            })
            ->whereHas('roles', function($query) {
                return $query->where('name', '=', \App\Models\User::ROLE_GUIDE);
            });

        if (is_valid_string($request->term)) {
            $searchString = '%' . $request->term . '%';

            $query->where(function($q) use ($searchString) {
                $q->where('first_name', 'like', $searchString)
                    ->orWhere('last_name', 'like', $searchString)
                    ->orWhere('email', 'like', $searchString);
            });
        }

        $scheduledUsers = $this->paginate($query, 9, $request->page);

        return response()->json([
                "data" => [
                    'items' => UserCollection::collection($scheduledUsers),
                    'pagination' => $this->mapPagination($scheduledUsers),
                ],
                "meta" => [
                    Permissions::make('schedules', [
                        'schedules.create',
                        'schedules.view',
                        'schedules.edit',
                        'schedules.delete',
                    ])
                ]
        ]);
    }

    public function users(Tour $tour)
    {
        $users = $tour->users;

        $preparedItems = collect();
        foreach ($users as $user) {
            $preparedItems->push([
                'id' => $user->id,
                'user_name' => $user->first_name . ' ' . $user->last_name
            ]);
        }

        return response()->json([
                "data" => [
                    'items' => $preparedItems
                ],
                "meta" => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['assigned_at'] = Carbon::createFromFormat('Y-m-d H:i', $validated['assigned_at'])
            ->format('Y-m-d');

        $schedule = Schedule::firstOrCreate($validated);

        return [
            'data' => [
                'form' => new Resource($schedule),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, \App\Models\User $user)
    {
        $query = $user->schedules()
            ->where('assigned_at', '>=', date('Y-m-d'))
            ->whereHas('excursion')
            ->orderBy('assigned_at', 'ASC');

        $schedules = $this->paginate($query, 9, $request->page);
        $schedules->load('tour', 'excursion');

        return response()->json([
            "data" => [
                'view' => $user,
                'items' => new ScheduleCollection($schedules),
                'pagination' => $this->mapPagination($schedules),
            ],
            "meta" => []
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tour  $tour
     * @return array
     */
    public function edit(Schedule $schedule)
    {
        return [
            'data' => [
                'form' => new Resource($schedule)
            ]
        ];
    }

    public function dateAutocomplete(Tour $tour)
    {
        $excursions = $tour->excursions()
            ->orderBy('day', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        return response()->json([
                "data" => [
                    'items' => Excursion::getForecasted($excursions, 7)
                ],
                "meta" => []
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return [
            'meta' => [
                Message::make(_('Schedule deleted'))->success(),
            ],
        ];
    }
}
