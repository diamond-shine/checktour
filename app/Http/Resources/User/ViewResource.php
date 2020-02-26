<?php

namespace App\Http\Resources\User;

// use Packages\ContactInfo\Resources\TelephoneResource;
use Packages\FileManager\Resources\FileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shelter\Guard\Models\User;

/**
 * Class ViewResource
 * @package Control\Packages\Users\Resources\User
 *
 * @property User $resource
 */
class ViewResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'email' => $this->resource->email,
            'full_name' => $this->resource->fullname(),
            'login' => $this->resource->login,
            'is_banned' => $this->resource->is_banned,
            'is_active' => $this->resource->is_active,
            'is_admin' => $this->resource->is_admin,
            'roles' => $this->resource->roles->implode('title', ', '),
            'avatar' => $this->resource->avatar(),
            'image' => $this->resource->image ?
                FileResource::make($this->resource->image) :
                null,
            // 'telephone' => $this->resource->telephone ?
            //     TelephoneResource::make($this->resource->telephone) :
            //     null,
        ];
    }
}
