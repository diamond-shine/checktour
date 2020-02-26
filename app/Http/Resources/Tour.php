<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Tour extends JsonResource
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
        $result['users_count'] = $this->resource->users()->count();
        $result['tour_options_count'] = $this->resource->tourOptions()->count();
        $result['excursions_count'] = $this->resource->excursions()->count();

        return $result;
    }
}
