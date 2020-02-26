<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\HandleBookings;
use App\Http\Requests\TourOption\{
    StoreRequest,
    UpdateRequest
};

use App\Helpers\Notification;

use DB;

use App\Models\TourOption;
use App\Models\Ticket;
use App\Models\Tour;


use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;

use App\Http\Resources\TourOption as Resource;
use App\Http\Resources\TourOptionCollection;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Tour $tour)
    {
        $query = TourOption::where('tour_id', $tour->id);

        if (is_valid_string($request->term)) {
            $query->where('name', 'like', "%{$request->term}%");
        }

        $options = $this->paginate($query, 9, $request->page);

        return response()->json([
                "data" => [
                    'items' => new TourOptionCollection($options),
                    'pagination' => $this->mapPagination($options),
                ],
                "meta" => [
                    Permissions::make('tour-options', [
                        'tour-options.create',
                        'tour-options.view',
                        'tour-options.edit',
                        'tour-options.delete',
                    ])
                ]
        ]);
    }

    public function all(Request $request, Tour $tour)
    {
        $options = TourOption::all();

        return response()->json([
                "data" => [
                    'items' => new TourOptionCollection($options),
                ],
                "meta" => [
                    Permissions::make('tour-options', [
                        'tour-options.list',
                        'tour-options.create',
                        'tour-options.view',
                        'tour-options.edit',
                        'tour-options.delete',
                    ])
                ]
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TourOption\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Tour $tour)
    {
        $tourOption = new TourOption($request->validated());
        $tourOption->save();
        return [
            'data' => [
                'form' => new Resource($tourOption),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tour $tour, TourOption $tourOption)
    {
        return [
            'data' => [
                'view' => new Resource($tourOption)
            ]
        ];
    }

    public function create(Tour $tour) {
        return [
            'data' => [
                'form' => [
                    'tour_id' => $tour->id,
                    'currency' => 'EUR',
                    'is_active' => false,
                ]
            ],
        ];
    }


    /**
     * Display the specified resource.
     *
     * @param  TourOption  $tourOption
     * @return array
     */
    public function edit(Tour $tour, TourOption $tourOption)
    {
        return [
            'data' => [
                'form' => new Resource($tourOption)
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
    public function update(UpdateRequest $request, Tour $tour, TourOption $tourOption)
    {
        $tourOption->fill($request->validated());
        DB::transaction(function () use ($tourOption) {
            Notification::option($tourOption);
            $tourOption->save();
            HandleBookings::onTourOptionUpdate($tourOption);
        }, 2);

        return [
            'data' => [
                'view' => new Resource($tourOption)
            ]
        ];
    }

    public function manage(Request $request)
    {
        if ($request->has('options')) {
            $options = $request->get('options');

            DB::transaction(function () use ($options) {
                foreach ($options as $item) {
                    TourOption::where('name', '=', $item['name'])
                        ->update(['is_active' => $item['is_active']]);

                    $updated = TourOption::where('name', '=', $item['name'])->get();

                    $updated->each(function($item) {
                        HandleBookings::onTourOptionUpdate($item);
                        Notification::option($item);
                    });
                }

            }, 2);
        }

        $options = TourOption::all();

        return [
            'data' => [
                'form' => new TourOptionCollection($options)
            ],
            'meta' => [
                Message::make(_('Tout options updated'))->success(),
            ],
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tour $tour, TourOption $tourOption)
    {
        $tourOption->delete();

        return [
            'meta' => [
                Message::make(_('Tour option deleted'))->success(),
            ],
        ];
    }
}
