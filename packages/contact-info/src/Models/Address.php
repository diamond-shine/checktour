<?php

namespace Shelter\ContactInfo\Models;

use Carbon\Carbon;
use Shelter\Kernel\Database\AbstractUUIDModel;
use Shelter\Shipping\Methods\NovaPoshtaDepartment;
use Shelter\Shipping\Methods\AbstractMethod as AbstractShippingMethod;
use Shipping;

use Illuminate\Database\Eloquent\{
    Collection as EloquentCollection,
    Relations\MorphMany,
    Relations\MorphOne,
    Builder,
    SoftDeletes
};

/**
 * Class Address
 * @package Shelter\ContactInfo\Models
 *
 * @property string $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $patronymic
 * @property string|null $country
 * @property string|null $region
 * @property string|null $city
 * @property string|null $street
 * @property string|null $additional
 * @property string|null $house_number
 * @property string|null $apartment_number
 * @property string|null $postcode
 * @property array $payload
 * @property string $addressable_type
 * @property string $addressable_id
 * @property boolean $is_default
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method Builder default(bool $flag = false)
 *
 * @property-read Telephone $telephone
 * @property-read EloquentCollection $telephones
 */
abstract class Address extends AbstractUUIDModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected static $variants = [
        'control' => Control\Address::class,
        'front' => Front\Address::class,
    ];

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $attributes = [
        'payload' => '[]',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Address $address) {
            if ($address->is_default) {
                static::default()
                    ->where('id', '<>', $address->id)
                    ->where('addressable_id', '=', $address->addressable_id)
                    ->where('addressable_type', '=', $address->addressable_type)
                    ->update([
                        'is_default' => 0,
                    ]);
            }
        });

        static::updated(function (Address $address) {
            if ($address->is_default) {
                static::default()
                    ->where('id', '<>', $address->id)
                    ->where('addressable_id', '=', $address->addressable_id)
                    ->where('addressable_type', '=', $address->addressable_type)
                    ->update([
                        'is_default' => 0,
                    ]);
            }
        });

        static::deleted(function (Address $address) {
            if ($address->is_default) {
                static::default()
                    ->where('id', '<>', $address->id)
                    ->where('addressable_id', '=', $address->addressable_id)
                    ->where('addressable_type', '=', $address->addressable_type)
                    ->limit(1)
                    ->update([
                        'is_default' => 1,
                    ]);
            }
        });
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
     * @return string
     */
    public function fullName(): string
    {
        return \trim("{$this->first_name} {$this->last_name} {$this->patronymic}");
    }

    /**
     * @return string
     */
    public function pretty(): string
    {
        $preview = [];

        if ($this->country) {
            $preview[] = $this->country;
        }

        if ($this->city) {
            $preview[] = $this->city;
        }

        if ($this->street) {
            $street = $this->street;

            if ($this->house_number) {
                $street .=  ' ' . _('буд.') . ' ' . $this->house_number;

                if ($this->apartment_number) {
                    $street .= ' ' . _('кв.') . ' ' . $this->apartment_number;
                }
            }

            $preview[] = $street;
        }

        return \implode(', ', $preview);
    }

    /**
     * @return string|null
     */
    public function method(): ?string
    {
        return $this->payload('shipping_method');
    }

    /**
     * @return AbstractShippingMethod|null
     */
    public function shippingMethod(): ?AbstractShippingMethod
    {
        return Shipping::find(
            $this->method()
        );
    }

    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function payload(string $path, $default = null)
    {
        return \array_get($this->payload, $path, $default);
    }

    /**
     * @param string|null $lang
     * @return string|null
     * @throws \Exception
     */
    public function novaposhtaCity(string $lang = null): ?string
    {
        if (! $cityId = $this->payload('novaposhta.city')) {
            return null;
        }

        /** @var NovaPoshtaDepartment $method */
        $method = Shipping::find('novaposhta.department');

        if (! $method) {
            return null;
        }

        return $method->cities($lang)->get($cityId);
    }

    /**
     * @param string|null $lang
     * @return string|null
     * @throws \Exception
     */
    public function novaposhtaDepartment(string $lang = null): ?string
    {
        $cityId = $this->payload('novaposhta.city');
        $departmentId = $this->payload('novaposhta.department');

        if (! $cityId || ! $departmentId) {
            return null;
        }

        /** @var NovaPoshtaDepartment $method */
        $method = Shipping::find('novaposhta.department');

        if (! $method) {
            return null;
        }

        $department = $method->departments($cityId, $lang)->get($departmentId);

        return $department['title'] ?? null;
    }

    /**
     * @return MorphMany
     */
    public function telephones(): MorphMany
    {
        return $this->makeTelephonesRelation(
            $this->resolveModelByContext(Telephone::class)
        );
    }

    /**
     * @return MorphOne
     */
    public function telephone(): MorphOne
    {
        return $this->makeTelephoneRelation(
            $this->resolveModelByContext(Telephone::class)
        );
    }
}
