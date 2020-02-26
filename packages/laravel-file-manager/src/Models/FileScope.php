<?php

namespace Ideil\LaravelFileManager\Models;

use Ideil\LaravelFileManager\Traits\AutoUuidTrait;
use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsToMany
};

/**
 * Class FileScope
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $id
 * @property string $scopable_key
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class FileScope extends Model
{
    use AutoUuidTrait;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsToMany
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class);
    }
}
