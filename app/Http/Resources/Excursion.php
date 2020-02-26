<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Excursion extends JsonResource
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
        $result['day_title'] = $this->resource->getDaytitle();

        return $result;
    }
}
