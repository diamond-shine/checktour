<?php

namespace Shelter\Guard\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Shelter\Kernel\Database\AbstractUUIDModel;

/**
 * Class Role
 * @package Shelter\Guard\Models
 *
 * @property string $id
 * @property string $email
 * @property string $token
 * @property array|null $payload
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserInvite extends AbstractUUIDModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $invite) {
            $key = config('app.key');

            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            $token = \hash_hmac(
                'sha256',
                Str::random(40),
                $key
            );

            $invite->token = $token;
        });
    }

    /**
     * @return Builder
     */
    public function isRelevant(): Builder
    {
        $expiredAt = $this->created_at->addMinutes(
            config('shelter.guard.invite_lifetime', 0)
        );

        return $this->created_at <= $expiredAt;
    }

    /**
     * @param string $driver
     * @return string
     */
    public function routeNotificationFor(string $driver): string
    {
        if ($driver === 'mail') {
            return $this->email;
        }

        throw new \InvalidArgumentException("Driver [{$driver}] not declared");
    }
}
