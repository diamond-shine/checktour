<?php

namespace App\Http\Controllers;

// use Control\Http\Controllers\Controller;
use Control\Packages\Users\Events\UserInviteSend;
use App\Notifications\InviteUser;
use App\Http\Resources\TreePermissions;
use Ideil\LaravelFileManager\Models\File;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;

use App\Tools\Meta\{
    Message,
    Permissions
};
use App\Http\Requests\User\{
    StoreRequest,
    SendInviteRequest,
    UpdateRequest
};
use App\Http\Resources\User\{
   FormResource,
    ListResource,
    ViewResource
};

use Shelter\Guard\Models\{
    User,
    UserInvite,
    UserRole
};
use Illuminate\Database\Eloquent\{
    Builder,
    ModelNotFoundException
};

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        /** @var Builder $query */
        $query = User::orderBy('first_name');

        if (is_valid_string($request->term)) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('first_name', 'like', "%{$request->term}%");
                $q->orWhere('last_name', 'like', "%{$request->term}%");
                $q->orWhere('email', 'like', "%{$request->term}%");
                $q->orWhere('login', 'like', "%{$request->term}%");

                // $q->orWhereHas('telephone', function (Builder $q) use ($request) {
                //     $q->where('number', 'like', "%{$request->term}%");
                // });
            });
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->paginate($query);

        $paginator->load('roles');

        return [
            'data' => [
                'items' => ListResource::collection($paginator),
                'pagination' => $this->mapPagination($paginator),
            ],
            'meta' => [
                Permissions::make('users', [
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'users.invite',
                    'user-roles.view',
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
            'data' => \array_merge(
                $this->mapEntityData(),
                [
                    'form' => [
                        'is_banned' => false,
                        'is_active' => false,
                        'is_admin' => false,
                        'roles' => [],
                        'permissions' => [],
                    ],
                ]
            ),
        ];
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request): array
    {
        $user = new User(
            Arr::except(
                $request->validated(),
                ['telephone', 'roles']
            )
        );

        $user->password = bcrypt($request->password);

        $user->save();

        $this->syncAttachments($user, $request);

        $user->save();

        return [
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->fullName(),
                ],
                'form' => FormResource::make($user),
            ],
            'meta' => [
                Message::make(_('User updated'))->success(),
            ],
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function view(User $user): array
    {
        return [
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->fullName(),
                ],
                'view' => ViewResource::make($user),
            ],
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function edit(User $user): array
    {
        return [
            'data' => \array_merge(
                $this->mapEntityData($user),
                [
                    'user' => [
                        'id' => $user->id,
                        'full_name' => $user->fullName(),
                    ],
                    'form' => FormResource::make($user),
                ]
            ),
            'meta' => [
                Permissions::make('users', [
                    'users.view',
                ]),
            ],
        ];
    }

    /**
     * @param User $user
     * @param UpdateRequest $request
     * @return array
     */
    public function update(User $user, UpdateRequest $request): array
    {
        $user->fill(
            Arr::except(
                $request->validated(),
                ['login', 'telephone', 'roles', 'image']
            )
        );

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if (auth()->user()->isAdmin()) {
            $this->syncAttachments($user, $request);
        }

        $user->save();

        return [
            'data' => [
                'form' => FormResource::make($user),
            ],
            'meta' => [
                Message::make(_('User updated'))->success(),
            ],
        ];
    }

    /**
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function destroy(User $user): array
    {
        $user->delete();

        return [
            'meta' => [
                Message::make(_('User deleted'))->success(),
            ],
        ];
    }

    /**
     * @param User $user
     * @param Request|StoreRequest|UpdateRequest $request
     * @return void
     * @throws InvalidArgumentException
     */
    protected function syncAttachments(User $user, Request $request): void
    {
        $user->roles()->detach();

        if ($request->has('roles')) {
            $roles = collect($request->get('roles', []))
                ->flatten()
                ->map(function ($role) {
                    if (\is_string($role)) {
                        try {
                            return UserRole::where(function (Builder $q) use ($role) {
                                $q->where('name', $role);
                                $q->orWhere('id', $role);
                            })->firstOrFail();
                        } catch (ModelNotFoundException $e) {
                            throw new InvalidArgumentException(
                                "User role with name [{$role}] not found"
                            );
                        }
                    }
                });

            $user->roles()->syncWithoutDetaching(
                $roles->pluck('id')
            );
        }

        $user->permissions()->delete();

        if ($request->has('permissions')) {
            $permissions = collect($request->get('permissions', []))
                ->flatten()
                ->map(function (string $permission) {
                    return [
                        'key' => $permission,
                    ];
                })
                ->toArray();

            $user->permissions()->createMany($permissions);
        }

        $user->files()->wherePivot('label', 'main')->detach();

        if ($request->image['id'] ?? null) {
            $user->attachFile(
                File::find($request->image['id']),
                'main'
            );
        }

        // if ($request->has('telephone.number')) {
        //     $user->telephone()->updateOrCreate([], [
        //         'number' => $request->telephone['number'],
        //         'is_default' => true,
        //     ]);
        // } else {
        //     $user->telephone()->delete();
        // }
    }

    /**
     * @param User|null $user
     * @return array
     */
    protected function mapEntityData(User $user = null): array
    {
        return [
            'roles' => $this->mapRoles($user),
            'permissions' => TreePermissions::collection(TreePermissions::ALPHABETICAL_LEAF_LAST),
            'leaf_only_permissions' => TreePermissions::leafOnly(),
        ];
    }

    /**
     * @param User|null $user
     * @return SupportCollection
     */
    protected function mapRoles(User $user = null): SupportCollection
    {
        $query = UserRole::defaultOrder();

        if ($user) {
            $query->with(['users']);
        }

        return $query
            ->get()
            ->map(function (UserRole $role) use ($user) {
                return [
                    'id' => $role->id,
                    'title' => $role->title,
                    'checked' => $user
                        && $role->users->contains('id', $user->id),
                ];
            });
    }

    /**
     * @return array
     */
    public function invite(): array
    {
        return [
            'data' => [
                'roles' => $this->mapRoles(),
                'permissions' => TreePermissions::collection(TreePermissions::ALPHABETICAL_LEAF_LAST),
            ],
            'meta' => [
                Permissions::make('user-invite', [
                    'users.invite.as-admin',
                ]),
            ],
        ];
    }

    /**
     * @param SendInviteRequest $request
     * @return array
     */
    public function sendInvite(SendInviteRequest $request): array
    {
        $invite = UserInvite::create([
            'email' => $request->email,
            'payload' => [
                'roles' => $request->roles ?? [],
                'permissions' => $request->permissions ?? [],
                'login' => $request->login,
                'as_admin' => (bool)$request->as_admin,
            ],
        ]);

        UserInvite::whereEmail($request->email)->where('id', '<>', $invite->id)->delete();

        Notification::send($invite, new InviteUser);

        return [
            'meta' => [
                Message::make(_('Invitation sent'))->success(),
            ],
        ];
    }
}
