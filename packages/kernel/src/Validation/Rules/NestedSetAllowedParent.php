<?php

namespace Shelter\Kernel\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class NestedSetAllowedParent implements Rule
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var null|array
     */
    protected $entityData;

    /**
     * @var string
     */
    protected $parentIdDBField;

    /**
     * UniqueSlug constructor.
     * @param string $modelClass
     * @param string $parentIdField
     */
    public function __construct(string $modelClass, string $parentIdField = 'parent_id')
    {
        $this->modelClass = $modelClass;
        $this->parentIdDBField = $parentIdField;
    }

    /**
     * @param $id
     * @param string $dbField
     * @return $this
     */
    public function entity($id, string $dbField = 'id'): self
    {
        $this->entityData = [
            'id' => $id,
            'field' => $dbField
        ];

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = $this->modelClass::where($this->parentIdDBField, $value);

        if ($this->entityData) {
            $query->whereNotDescendantOf($this->entityData['id']);
            $query->where(
                $this->entityData['field'],
                '<>',
                $this->entityData['id']
            );
        }

        return $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid parent.';
    }
}
