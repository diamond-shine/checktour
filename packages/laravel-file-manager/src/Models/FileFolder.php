<?php

namespace Ideil\LaravelFileManager\Models;

use Ideil\LaravelFileManager\Traits\AutoUuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\Collection as NestedsetCollection;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class FileFolder
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $id
 * @property string $name
 * @property string|null $mark
 * @property int $_lft
 * @property int $_rgt
 * @property string $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $files

 * @method Builder belongsToFolder(string|FileFolder $folder)
 */
class FileFolder extends Model
{
    use AutoUuidTrait, NodeTrait, ExtendableModelTrait;

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
     * @param Builder $q
     * @param string|FileFolder $folder
     * @return Builder
     */
    public function scopeBelongsToFolder(Builder $q, $folder): Builder
    {
        $id = $folder instanceof self ?
            $folder->id :
            $folder;

        return $q->where('parent_id', $id);
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * @return NestedsetCollection
     */
    public function trace(): NestedsetCollection
    {
        return NestedsetCollection::make($this->ancestors)
            ->push($this)
            ->toFlatTree();
    }
}
