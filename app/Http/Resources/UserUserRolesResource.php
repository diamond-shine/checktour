<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Shelter\Guard\Models\UserRole;
use Illuminate\Support\Collection;

/**
 * Class UserUserRolesResource
 * @package Control\Packages\Users\Resources
 */
class UserUserRolesResource extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Collection
     */
    public function toArray($request): Collection
    {
        return $this->collection->map(function (UserRole $role) {
            return [
                'id' => $role->getKey(),
                'title' => $role->title,
                'name' => $role->name,
            ];
        });
    }
}
