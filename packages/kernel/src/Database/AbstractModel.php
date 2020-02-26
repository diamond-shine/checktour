<?php

namespace Shelter\Kernel\Database;

use InvalidArgumentException;
use Shelter\Kernel\Injections\ModelRelations\HotRelationsTrait;
use Illuminate\Database\Eloquent\{
    Builder,
    Model
};
use Ideil\MorphAndBelongsToOneRelations\RelationsTrait;

/**
 * Class AbstractModel
 * @package Shelter\Kernel\Database
 *
 * @method Builder defaultOrder(string $direction = 'DESC')
 * @method Builder search($term, array $fields, string $wildcard = 'both')
 */
abstract class AbstractModel extends Model
{
    use HotRelationsTrait, RelationsTrait;

    /**
     * @var array|string
     */
    protected static $defaultOrderColumn = 'updated_at';

    /**
     * @var string
     */
    protected static $defaultOrderDirection = 'DESC';

    /**
     * @param Builder $q
     * @param string $direction
     * @return Builder
     */
    public function scopeDefaultOrder(Builder $q, string $direction = null): Builder
    {
        return $q->orderBy(
            static::$defaultOrderColumn,
            $direction ?? static::$defaultOrderDirection
        );
    }

    /**
     * @return string
     */
    public static function morphMapType(): string
    {
        return \real_snake_case(
            \class_basename(static::class)
        );
    }

    /**
     * @param Builder $q
     * @param mixed $term
     * @param array $fields
     * @param string $wildcard
     * @return Builder
     */
    public function scopeSearch(Builder $q, $term, array $fields, string $wildcard = 'both'): Builder
    {
        if (! \is_valid_string($term)) {
            return $q;
        }

        switch ($wildcard) {
            case 'both':
                $term = "%{$term}%";
                break;

            case 'left':
                $term = "%{$term}";
                break;

            case 'right':
                $term = "{$term}%";
                break;

            default:
                throw new InvalidArgumentException('Wildcard allowed values: both, right, left');
        }

        return $q->where(function (Builder $q) use ($term, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'like', $term);
            }
        });
    }
}
