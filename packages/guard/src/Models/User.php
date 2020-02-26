<?php

namespace Shelter\Guard\Models;

use Carbon\Carbon;
use Ideil\MorphAndBelongsToOneRelations\Relations\MorphToOne;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shelter\ContactInfo\Traits\HasControlContactInfo;
use Shelter\Kernel\Database\AbstractUUIDModel;

use Ideil\LaravelFileManager\{
    Traits\HasFilesTrait,
    Models\File
};

use Shelter\Guard\{
    Permissions\Permissionable,
    Traits\CanResetPassword,
    Traits\Permissions
};

use Illuminate\Contracts\Auth\{
    Access\Authorizable as AuthorizableContract,
    Authenticatable as AuthenticatableContract,
    CanResetPassword as CanResetPasswordContract
};

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Relations\BelongsToMany,
    SoftDeletes
};

/**
 * Class User
 * @package Shelter\Guard\Models
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $login
 * @property boolean $is_admin
 * @property boolean $is_banned
 * @property boolean $is_active
 * @property Carbon|null $last_logged_at
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection $roles
 * @property-read File|null $image
 * @property-read Collection $permissions
 */
class User extends AbstractUUIDModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    Permissionable
{
    use SoftDeletes, Authenticatable, CanResetPassword, Notifiable, HasFilesTrait, Permissions, HasControlContactInfo;

    /**
     * @var string
     */
    public $table = 'users';

    /**
     * @var array
     */
    protected $guarded = [
        'id',
        'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_logged_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return implode(
            ' ',
            array_filter([
                $this->first_name,
                $this->last_name,
            ])
        );
    }

    /**
     * @return string
     */
    public function avatar(): string
    {
        if ($this->image) {
            return $this->image->thumb('400x400');
        }

        return $this->gravatar();
    }

    /**
     * @return string
     */
    public function gravatar(): string
    {
        $hash = \md5(
            \strtolower(
                \trim($this->email)
            )
        );

        return "https://www.gravatar.com/avatar/{$hash}";
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class);
    }

    /**
     * @return MorphToOne
     */
    public function image(): MorphToOne
    {
        return $this->file()->wherePivot('label', 'main');
    }
}
