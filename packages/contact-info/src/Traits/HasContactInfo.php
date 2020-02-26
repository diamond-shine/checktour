<?php

namespace Shelter\ContactInfo\Traits;

use Illuminate\Database\Eloquent\{
    Collection as EloquentCollection,
    Relations\MorphOne,
    Relations\MorphMany
};

use Shelter\ContactInfo\Models\{
    Address,
    Telephone
};

/**
 * Trait HasContactInfo
 * @package Shelter\ContactInfo\Traits
 *
 * @property Address|null $address
 * @property Telephone|null $telephone
 * @property EloquentCollection $addresses
 * @property EloquentCollection $telephones
 */
trait HasContactInfo
{
    /**
     * @return MorphOne
     */
    public function address(): MorphOne
    {
        return $this->makeAddressRelation(
            $this->resolveModelByContext(Address::class)
        );
    }

    /**
     * @return MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->makeAddressesRelation(
            $this->resolveModelByContext(Address::class)
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

    /**
     * @return MorphMany
     */
    public function telephones(): MorphMany
    {
        return $this->makeTelephonesRelation(
            $this->resolveModelByContext(Telephone::class)
        );
    }
}
