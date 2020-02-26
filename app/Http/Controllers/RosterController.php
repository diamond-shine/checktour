<?php

namespace App\Http\Controllers;

use App\Helpers\HandleBookings;
use Carbon\Carbon;
use Ideil\LaravelFileManager\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Helpers\Notification;
use App\Helpers\Logging;

use App\Http\Resources\Roster as Resource;
use App\Http\Resources\RosterCollection;
use App\Http\Resources\CommentCollection;

use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;

use App\Http\Requests\Roster\UpdateRequest;

use App\Models\Schedule;
use DB;

class RosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Schedule::where('assigned_at', '=', Carbon::now()->format('Y-m-d'))
            ->whereHas('excursion');

        $currentUser = auth()->user();

        if (is_valid_string($request->term)) {
            $query->where(function($query) use($request) {
                $query->orWhereHas('user', function($query) use($request) {
                    $query->where('first_name', 'like', "%{$request->term}%")
                    ->orWhere('last_name', 'like', "%{$request->term}%");

                    if (filter_var($request->term, FILTER_VALIDATE_EMAIL )) {
                        $query->orWhere('email', 'like', "%{$request->term}%");
                    }
                });

                $query->orWhereHas('tour', function($query) use($request) {
                    $query->where('name', 'like', "%{$request->term}%");
                });
            });
        }

        if (!$currentUser->isAdmin() && !$currentUser->hasTourConciergeRole()) {
            $query->where('user_id', '=', $currentUser->id);
        }

        if ($tourId = $request->get('tour_id', null)) {
            $query->where('tour_id', '=', $tourId);
        }

        if ($startTime = $request->get('start_time', null)) {
            $query->whereHas('excursion', function($query) use ($startTime) {
                $query->where('time', '>=', $startTime);
            });
        }

        if ($endTime = $request->get('end_time', null)) {
            $query->whereHas('excursion', function($query) use ($endTime) {
                $query->where('time', '<=', $endTime);
            });
        }

        $schedules = $this->paginate($query->orderBy('assigned_at', 'DESC'), 9, $request->page);
        $schedules->load('tour', 'user', 'excursion');

        return response()->json([
                "data" => [
                    'items' => new RosterCollection($schedules),
                    'pagination' => $this->mapPagination($schedules),
                ],
                "meta" => [
                    Permissions::make('rosters', [
                        'rosters.list',
                        'rosters.view',
                        'rosters.edit',
                        'rosters.permit',
                        'rosters.process',

                        'tours.list'
                    ])
                ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $roster)
    {
        $roster->load(
            'excursion',
            'tour',
            'tour.tourOptions',
            'user'
        );

        $comments = $roster->comments()->orderBy('created_at', 'DESC')->get();
        $comments->load('commented');

        return [
            'data' => [
                'view' => new Resource($roster),
                'comments' => new CommentCollection($comments)
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Schedule $roster)
    {
        $beforeSnapshot = Logging::rosterSnapshot($roster);
        $validated = $request->validated();
        $roster->fill($validated);

        DB::transaction(function () use ($roster, $validated) {
            $images = Arr::get($validated, 'images', []);

            foreach ($images as $image) {
                if ($image['id'] ?? null) {
                    $roster->attachFile(File::find($image['id']), 'main');
                }
            }

            $roster->files()
                ->wherePivotNotIn('file_id', array_column($images, 'id'))
                ->detach();

            if (!empty($validated['comment'])) {
                $currentUser = auth()->user();
                $currentUser->comment($roster, $validated['comment']);
            }

            Notification::roster($roster);

            $roster->save();
            HandleBookings::onScheduleUpdate($roster);
        }, 2);

        $roster->load(
            'excursion',
            'tour',
            'tour.tourOptions',
            'user'
        );

        Logging::roster($roster, $beforeSnapshot);

        $comments = $roster->comments()->orderBy('created_at', 'DESC')->get();
        $comments->load('commented');

        return [
            'data' => [
                'view' => new Resource($roster),
                'comments' => new CommentCollection($comments)
            ]
        ];
    }
}
