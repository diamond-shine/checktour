<?php

namespace Ideil\LaravelFileManager\Models;

use Ideil\LaravelFileManager\Traits\AutoUuidTrait;
use Ideil\LaravelFileManager\Traits\HasFilesTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class FileCollection
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $id
 * @property string|null $title
 * @property string|null $label
 * @property string|null $entity_type
 * @property string|null $entity_id
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $files
 */
class FileCollection extends Model
{
    use AutoUuidTrait, HasFilesTrait, ExtendableModelTrait;

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
     * @return MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this
            ->morphToMany(
                File::class,
                'entity',
                'file_shortcuts'
            )
            ->orderBy('weight', 'ASC')
            ->withPivot([
                'weight',
                'payload',
                'conversion'
            ]);
    }
}
