<?php

namespace App\Http\Resources\User;

// use Control\Packages\ContactInfo\Resources\TelephoneResource;
use Packages\FileManager\Resources\FileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shelter\Guard\Models\User;

/**
 * Class FormResource
 * @package Control\Packages\Users\Resources\User
 *
 * @property User $resource
 */
class FormResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'email' => $this->resource->email,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'login' => $this->resource->login,
            'is_banned' => $this->resource->is_banned,
            'is_active' => $this->resource->is_active,
            'is_admin' => $this->resource->is_admin,
            'roles' => $this->resource->roles->pluck('id'),
            'permissions' => $this->resource->permissions->pluck('key'),
            'image' => $this->resource->image ?
                FileResource::make($this->resource->image) :
                null,
            'gravatar' => $this->resource->gravatar(),
            // 'telephone' => $this->resource->telephone ?
            //     TelephoneResource::make($this->resource->telephone) :
            //     null,
        ];
    }
}
