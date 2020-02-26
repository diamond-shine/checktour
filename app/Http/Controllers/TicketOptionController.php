<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TicketOption\StoreRequest;
use App\Http\Requests\TicketOption\UpdateRequest;
use App\Tools\Meta\Message;

use App\Models\TicketOption;
use App\Models\Ticket;
use App\Models\TourOption;

use App\Http\Resources\TicketOptionCollection;
use App\Http\Resources\TicketOption as TicketOptionResource;
use App\Http\Resources\Ticket as TicketResource;
use App\Http\Resources\Tour as TourResource;



class TicketOptionController extends Controller
{
    /**
     * @return array
     */
    public function index(Request $request, Ticket $ticket)
    {
        $query = $ticket->ticketOptions();

        $ticketOptions = $this->paginate($query, 9, $request->page);
        $ticketOptions->load('tourOption');

        return response()->json([
                "data" => [
                    'ticket' => new TicketResource($ticket),
                    'items' => new TicketOptionCollection($ticketOptions),
                    'pagination' => $this->mapPagination($ticketOptions),
                ],
                "meta" => []
        ]);
    }

    /**
     * @return array
     */
    public function create(Ticket $ticket): array
    {
        return [
            'data' => [
                'form' => [
                    'tour_option_id' => $ticket->tour->id,
                    'ticket_id' => $ticket->id,
                    'is_active' => false,
                ],
                // 'tour' => new TourResource($ticket->tour)
            ],
        ];
    }

    public function autocomplete(Ticket $ticket)
    {
        // $options = $ticket->tour->tourOptions;
        // TODO get option for ticket

        $options = $ticket->tour->tourOptions;

        return [
            'data' => [
                'items' => $options->map->only('id', 'name')
            ]
        ];
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

        Ticket::where('id', '=', $validated['ticket_id'])->firstOrfail();
        TourOption::where('id', '=', $validated['tour_option_id'])->firstOrfail();

        $ticketOption = TicketOption::firstOrCreate([
                'ticket_id' => $validated['ticket_id'],
                'tour_option_id' => $validated['tour_option_id']
            ],
            $validated
        );

        $ticketOption->save();

        return [
            'data' => [
                'form' => new TicketOptionResource($ticketOption),
            ],
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(UpdateRequest $request, Ticket $ticket, TicketOption $ticketOption)
    // {
    //     $validated = $request->validated();
    //     TourOption::where('id', '=', $validated['tour_option_id'])->firstOrfail();

    //     $ticketOption->fill($validated);
    //     $ticketOption->save();

    //     return [
    //         'data' => [
    //             'view' => new TicketOptionResource($ticketOption)
    //         ]
    //     ];
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket, TicketOption $ticketOption)
    {
        $ticketOption->delete();

        return [
            'meta' => [
                Message::make(_('Ticket option deleted'))->success(),
            ],
        ];
    }
}
