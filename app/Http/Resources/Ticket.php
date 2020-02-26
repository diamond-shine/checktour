<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ticket extends JsonResource
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

        $result['ticket_opions_count'] = $this->resource->ticketOptions->count();
        $result['total_price'] = $this->resource->ticketOptions->sum(function ($option) {
            return $option->price;
        }) + $this->resource->price;

        $result['total_price'] = number_format($result['total_price'], 2, '.', '');

        return $result;
    }
}


