<?php

namespace Shelter\Kernel\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueNestedSet implements Rule
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var null|array
     */
    protected $ignored;

    /**
     * @var null|array
     */
    protected $parentData;

    /**
     * UniqueSlug constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param $value
     * @param string|null $dbField
     * @return $this
     */
    public function ignore($value, string $dbField = 'id'): self
    {
        $this->ignored = [
            'field' => $dbField,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param $id
     * @param string $dbField
     * @return $this
     */
    public function parent($id, string $dbField = 'parent_id'): self
    {
        $this->parentData = [
            'id' => $id,
            'field' => $dbField,
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
        $query = \DB::table($this->table)->where($attribute, $value);

        if ($this->parentData) {
            $query
                ->where(
                    $this->parentData['field'],
                    $this->parentData['id']
                );
        } else {
            $query->whereNull('parent_id');
        }

        if ($this->ignored) {
            $query->where(
                $this->ignored['field'],
                '<>',
                $this->ignored['value']
            );
        }

        return ! $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be unique.';
    }
}
