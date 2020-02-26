<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'comment' => $this->resource->comment,
            'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->created_at)
                ->format('H:i, d F Y'),
            'id' => $this->resource->id,
            'first_name' => $this->resource->commented->first_name,
            'last_name' => $this->resource->commented->last_name
        ];
    }
}
