<?php

namespace Control\Packages\Users\Http\Controllers;

use Control\Http\Controllers\Controller;
use Control\Packages\Users\Resources\TreePermissions;
use Control\Tools\Meta\Message;
use Control\Tools\Meta\Permissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shelter\Guard\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;

use Control\Packages\Users\Http\Requests\UserRole\{
    CreateRequest,
    UpdateRequest
};

use Control\Packages\Users\Resources\UserRole\{
    FormResource,
    ListResource,
    ViewResource
};

class UserRolesController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        /** @var Builder $query */
        $query = UserRole::orderBy('title');

        if (is_valid_string($request->term)) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('title', 'like', "%{$request->term}%");
                $q->orWhere('name', 'like', "%{$request->term}%");
            });
        }

        $userRoles = $this->paginate($query, 15, $request->page);

        return [
            'data' => [
                'items' => ListResource::collection($userRoles),
                'pagination' => $this->mapPagination($userRoles),
            ],
            'meta' => [
                Permissions::make('user-roles', [
                    'user-roles.create',
                    'user-roles.view',
                    'user-roles.edit',
                    'user-roles.delete',
                ]),
            ],
        ];
    }

    /**
     * @return array
     */
    public function create(): array
    {
        return [
            'data' => $this->mapEntityData(),
        ];
    }

    /**
     * @param CreateRequest $request
     * @return array
     */
    public function store(CreateRequest $request): array
    {
        $userRole = new UserRole(
            $request->validated()
        );

        $userRole->save();

        $this->syncPermissions($userRole, $request);

        return [
            'data' => [
                'form' => FormResource::make($userRole),
            ],
            'meta' => [
                Message::make(_('Роль пользователя создан'))->success(),
            ],
        ];
    }

    /**
     * @param UserRole $userRole
     * @return array
     */
    public function view(UserRole $userRole): array
    {
        return [
            'data' => [
                'view' => ViewResource::make($userRole),
            ],
        ];
    }

    /**
     * @param UserRole $userRole
     * @return array
     */
    public function edit(UserRole $userRole): array
    {
        return [
            'data' => array_merge(
                $this->mapEntityData($userRole), [
                    'form' => new FormResource($userRole),
                ]
            ),
        ];
    }

    /**
     * @param UserRole $userRole
     * @param UpdateRequest $request
     * @return array
     */
    public function update(UserRole $userRole, UpdateRequest $request): array
    {
        $userRole->fill(
            $request->validated()
        );

        $userRole->save();

        $this->syncPermissions($userRole, $request);

        return [
            'data' => [
                'form' => new FormResource($userRole),
            ],
            'meta' => [
                Message::make(_('Інформація про роль змінена'))->success(),
            ],
        ];
    }

    /**
     * @param UserRole $userRole
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function destroy(UserRole $userRole)
    {
        if ($userRole->users()->exists()) {
            return $this->failedResponseWithMessage(
                _('Неможливо видалити роль яка використовується')
            );
        }

        $userRole->delete();

        return [];
    }

    /**
     * @param UserRole $userRole
     * @param Request $request
     * @return void
     */
    protected function syncPermissions(UserRole $userRole, Request $request): void
    {
        $userRole->permissions()->delete();

        $permissions = collect($request->get('permissions', []))
            ->flatten()
            ->map(function (string $permission) {
                return [
                    'key' => $permission,
                ];
            })
            ->toArray();

        $userRole->permissions()->createMany($permissions);
    }

    /**
     * @param UserRole|null $userRole
     * @return array
     */
    protected function mapEntityData(UserRole $userRole = null): array
    {
        return [
            'permissions' => TreePermissions::collection(TreePermissions::ALPHABETICAL_LEAF_LAST),
        ];
    }
}
