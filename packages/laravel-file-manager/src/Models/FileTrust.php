<?php

namespace Ideil\LaravelFileManager\Models;

use Ideil\LaravelFileManager\Traits\AutoUuidTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FileTrust
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $id
 * @property string $file_id
 * @property string $file_trastuble_type
 * @property string $file_trastuble_key
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class FileTrust extends Model
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
}
