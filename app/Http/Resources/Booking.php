<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TicketOptionCollection as TicketOptionCollection;
use App\Http\Resources\Schedule as ScheduleResource;
use App\Http\Resources\BookingTicketCollection;
use Carbon\Carbon;

class Booking extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);
        unset($result['source_data']);

        $result['booking_tickets'] = new BookingTicketCollection($this->resource->bookingTickets);

        $result['start_total_price'] = $this->resource->bookingTickets->sum(function ($bookingTicket) {
            return $bookingTicket->total_price;
        });

        $result['total_price'] = $this->resource->bookingTickets->sum(function ($bookingTicket) {
            return $bookingTicket->getPrice();
        });

        $result['tickets_count'] = $this->resource->bookingTickets->sum('quantity');
        $result['currency'] = $this->resource->tour->currency;

        $result['start_at_full'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->start_at)
            ->format('H:i, d M Y');

        $result['start_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->start_at)
            ->format('M d, H:i');
        $result['end_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->end_at)
            ->format('M d, H:i');

        $result['schedule'] = new ScheduleResource($this->whenLoaded('schedule'));

        $result['ticket_options'] = new TicketOptionCollection($this->whenLoaded('ticketOptions'));

        return $result;
    }
}
