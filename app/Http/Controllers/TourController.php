<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Tour\{
    StoreRequest,
    UpdateRequest
};

use App\Models\Tour;
use Shelter\Guard\Models\User;

use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;

use App\Http\Resources\Tour as Resource;
use App\Http\Resources\TourCollection;


class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Tour::query();

        if (is_valid_string($request->term)) {
            $query->where('name', 'like', "%{$request->term}%");
        }

        $tours = $this->paginate($query, 9, $request->page);

        $tours->load('users');

        return response()->json([
            "data" => [
                'items' => new TourCollection($tours),
                'pagination' => $this->mapPagination($tours),
            ],
            "meta" => [
                Permissions::make('tours', [
                    'excursions.list',

                    'tickets.list',

                    'tours.create',
                    'tours.view',
                    'tours.edit',
                    'tours.delete',

                    'tours-users.view',
                    'tours-users.edit',

                    'tour-options.list',

                    'schedules.list',

                    'users.list'
                ])
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $tour = new Tour($request->validated());
        $tour->save();
        $tour->load('users');
        return [
            'data' => [
                'form' => new Resource($tour),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  Tour  $tour
     * @return array
     */
    public function edit(Tour $tour)
    {
        return [
            'data' => [
                'form' => new Resource($tour)
            ]
        ];
    }


    /**
     * Display the specified resource.
     *
     * @param  Tour  $tour
     * @return array
     */
    public function show(Tour $tour)
    {
        $tour->load('users');
        return [
            'data' => [
                'view' => new Resource($tour)
            ],
            "meta" => [
                Permissions::make('tours', [
                    'excursions.list',

                    'tickets.list',

                    'tours.create',
                    'tours.view',
                    'tours.edit',
                    'tours.delete',

                    'tour-options.list',
                    'tours-users.view',
                    'tours-users.edit',

                    'schedules.list',

                    'users.list'
                ])
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
    public function update(UpdateRequest $request, Tour $tour)
    {
        $tour->fill($request->validated());
        $tour->save();

        return [
            'data' => [
                'view' => new Resource($tour)
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Tour $tour)
    // {
    //     $tour->delete();

    //     return [
    //         'meta' => [
    //             Message::make(_('Tour deleted'))->success(),
    //         ],
    //     ];
    // }

    public function attachUser(Request $request, Tour $tour)
    {
        // $tour = Tour::where('id', '=', $request->get('tour_id'))->firstOrFail();
        $user = User::where('id', '=', $request->get('user_id'))->firstOrFail();

        $tour->users()->syncWithoutDetaching([$request->get('user_id')]);

        return [
            'data' => [
                'form' => ['tour_id' => $user->id, 'user_id' => $tour->id],
            ],
        ];
    }

    public function detachUser(Tour $tour, User $user)
    {
        $tour->users()->detach([$user->id]);

        return [
            'meta' => [
                Message::make(_('User deleted'))->success(),
            ],
        ];
    }




    public function usersAutocomplete()
    {
        $users = User::get();

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

    public function users(Tour $tour)
    {
        $users = $tour->users;

        return response()->json([
                "data" => [
                    'items' => $users->map->only('id', 'first_name', 'last_name', 'email')
                ],
                "meta" => [
                    Permissions::make('tours', [
                        'tours-users.view',
                        'tours-users.edit',
                        'tours.create',
                        'tours.view',
                        'tours.edit',
                        'tours.delete',
                    ])
                ]
        ]);
    }
}
