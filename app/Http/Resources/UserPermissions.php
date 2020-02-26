<?php

namespace App\Http\Resources;

use Control\Packages\Users\Resources\UserUserRolesResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserPermissions
 * @package Control\Packages\Users\Resources
 */
class UserPermissions extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'roles' => new UserUserRolesResource($this->roles),
            'can' => $this->allPermissions(),
        ];
    }
}
