<?php

namespace Shelter\ContactInfo\Traits;

use Illuminate\Database\Eloquent\{
    Collection as EloquentCollection,
    Relations\MorphOne,
    Relations\MorphMany
};

use Shelter\ContactInfo\Models\Control\{
    Address,
    Telephone
};

/**
 * Trait HasControlContactInfo
 * @package Shelter\ContactInfo\Traits
 *
 * @property Address|null $address
 * @property Telephone|null $telephone
 * @property EloquentCollection $addresses
 * @property EloquentCollection $telephones
 */
trait HasControlContactInfo
{
    /**
     * @return MorphOne
     */
    public function address(): MorphOne
    {
        return $this->makeAddressRelation(Address::class);
    }

    /**
     * @return MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->makeAddressesRelation(Address::class);
    }

    /**
     * @return MorphOne
     */
    public function telephone(): MorphOne
    {
        return $this->makeTelephoneRelation(Telephone::class);
    }

    /**
     * @return MorphMany
     */
    public function telephones(): MorphMany
    {
        return $this->makeTelephonesRelation(Telephone::class);
    }
}
