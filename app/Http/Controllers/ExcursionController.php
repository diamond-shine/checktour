<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Excursion\{
    StoreRequest,
    UpdateRequest
};

use App\Http\Resources\Tour as TourResource;
use App\Http\Resources\Excursion as Resource;
use App\Http\Resources\ExcursionCollection;

use App\Models\Excursion;
use App\Models\Tour;


use App\Tools\Meta\Message;
use App\Tools\Meta\Permissions;


class ExcursionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Tour $tour)
    {
        $query = $tour->excursions();

        if (is_valid_string($request->term)) {
            $query->where('name', 'like', "%{$request->term}%");
        }

        $excursions = $this->paginate($query->orderBy('day', 'ASC')->orderBy('time', 'ASC'), 9, $request->page);

        return response()->json([
                "data" => [
                    'items' => new ExcursionCollection($excursions),
                    'pagination' => $this->mapPagination($excursions),
                    'tour' => new TourResource($tour)
                ],
                "meta" => [
                    Permissions::make('excursions', [
                        'excursions.create',
                        'excursions.view',
                        'excursions.edit',
                        'excursions.delete',
                    ])
                ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Tour $tour): array
    {
        return [
            'data' => [
                'form' => [
                    'tour_id' => $tour->id,
                    'ticket_id' => $tour->id,
                ]
            ],
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Tour $tour)
    {
        $validatedData = $request->validated();

        $excursion = Excursion::firstOrCreate($validatedData);

        return [
            'data' => [
                'form' => new Resource($excursion),
                // 'tour' => new TourCollection(Tour::query()->active()->get())
            ],
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tour $tour, Excursion $excursion)
    {
        $excursion->delete();

        return [
            'meta' => [
                Message::make(_('Excursion deleted'))->success(),
            ]
        ];
    }
}
