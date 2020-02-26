<?php

namespace Shelter\ContactInfo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use libphonenumber\PhoneNumberFormat;
use Shelter\Kernel\Database\AbstractUUIDModel;

/**
 * Class Telephone
 * @package Shelter\ContactInfo\Models
 *
 * @property string $number
 * @property string $telephonable_type
 * @property string $telephonable_id
 * @property boolean $is_default
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method Builder default(bool $flag = false)
 * @method Builder telephonable(string $entityClass, string $id = null, bool $ignore = true)
 * @method Builder whereNumber(string $number)
 */
abstract class Telephone extends AbstractUUIDModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected static $variants = [
        'control' => Control\Telephone::class,
        'front' => Front\Telephone::class,
    ];

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Telephone $telephone) {
            if ($telephone->is_default) {
                static::default()
                    ->where('id', '<>', $telephone->id)
                    ->where('telephonable_id', '=', $telephone->telephonable_id)
                    ->where('telephonable_type', '=', $telephone->telephonable_type)
                    ->update([
                        'is_default' => 0,
                    ]);
            }
        });

        static::updated(function (Telephone $telephone) {
            if ($telephone->is_default) {
                static::default()
                    ->where('id', '<>', $telephone->id)
                    ->where('telephonable_id', '=', $telephone->telephonable_id)
                    ->where('telephonable_type', '=', $telephone->telephonable_type)
                    ->update([
                        'is_default' => 0,
                    ]);
            }
        });
    }

    /**
     * @param string $value
     */
    public function setNumberAttribute(string $value): void
    {
        $this->attributes['number'] = static::normalize($value);
    }

    /**
     * @param string $value
     */
    public function setRawNumberAttribute(string $value): void
    {
        $this->attributes['number'] = $value;
    }

    /**
     * @param Builder $q
     * @param bool $flag
     * @return Builder
     */
    public function scopeDefault(Builder $q, bool $flag = true): Builder
    {
        return $q->whereIsDefault($flag);
    }

    /**
     * @param Builder $q
     * @param string $entityClass
     * @param string|null $id
     * @param bool $ignore
     * @return Builder
     */
    public function scopeTelephonable(
        Builder $q,
        string $entityClass,
        string $id = null,
        bool $ignore = false
    ): Builder {
        $q->where('telephonable_type', (new $entityClass)->getMorphClass());

        if ($id !== null) {
            $q->where(
                'telephonable_id',
                ! $ignore ? '=' : '<>',
                $id
            );
        }

        return $q;
    }

    /**
     * @param Builder $q
     * @param string $number
     * @return Builder
     */
    public function scopeWhereNumber(Builder $q, string $number): Builder
    {
        return $q->where(
            'number',
            static::normalize($number)
        );
    }

    /**
     * @param int|null $format
     * @param array $countries
     * @return string
     */
    public function format(int $format = null, array $countries = []): string
    {
        try {
            return \phone($this->number, $countries, $format);
        } catch (\Exception $e) {
            return $this->number;
        }
    }

    /**
     * @param string $number
     * @param array $countries
     * @return string
     */
    public static function normalize(string $number, array $countries = []): string
    {
        return \phone($number, $countries, PhoneNumberFormat::E164);
    }
}
