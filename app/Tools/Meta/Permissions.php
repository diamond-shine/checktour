<?php

namespace App\Tools\Meta;

class Permissions extends AbstractMeta
{
    /**
     * @var string
     */
    protected $scope;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * @param string $type
     * @param array $names
     */
    public function __construct(string $type, array $names)
    {
        $this->scope = $type;
        $this->permissions = $names;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'permissions';
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        $result = [];
        $user = app('shelter.auth')->user();

        foreach ($this->permissions as $index => $permission) {
            $args = [];

            if (\is_array($permission)) {
                $args = $permission;
                $permission = $index;
            }

            if ($user->isAdmin() || $user->can($permission, $args)) {
                $result[] = $permission;
            }
        }

        return [
            'scope' => $this->scope,
            'permissions' => $result,
        ];
    }
}
