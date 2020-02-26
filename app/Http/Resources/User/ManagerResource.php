<?php

namespace Control\Packages\Users\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Control\Packages\ContactInfo\Resources\TelephoneResource;

/**
 * Class ManagerResource
 * @package Control\Packages\Users\Resources\User
 */
class ManagerResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'full_name' => $this->resource->fullname(),
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'telephone' => $this->when($this->resource->telephone, function () {
                return TelephoneResource::make($this->resource->telephone);
            }),
        ];
    }
}
