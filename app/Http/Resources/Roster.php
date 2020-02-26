<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Packages\FileManager\Resources\FileResource;

use App\Http\Resources\User\ListResource as UserResource;

class Roster extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ticketsSummary = $this->resource->getTicketsSummary();

        $result = parent::toArray($request);
        $result['user'] = new UserResource($this->whenLoaded('user'));
        $result['currency'] = $this->resource->tour->currency;
        $result['total_price'] = $ticketsSummary['total_price'];
        $result['tickets_count'] = $ticketsSummary['total_count'];
        $result['tickets_by_types'] = $ticketsSummary['by_types'];
        $result['assigned_at_formated'] = Carbon::createFromFormat(
            'Y-m-d H:i', $this->resource->assigned_at .' ' . $this->resource->excursion->time
        )->format('H:i');

        if (!$result['disabled_options']) {
            $result['disabled_options'] = [];
        }

        if (!empty($this->resource->images)) {
            foreach ($this->resource->images as $image) {
                $result['images'][] = FileResource::make($image);
            }
        }

        return $result;
    }
}
