<?php

namespace App\Http\Resources;

/**
 * Class TreePermissions
 * @package Control\Packages\Users\Resources
 */
class TreePermissions
{
    public const NONE = 0;
    public const ALPHABETICAL = 1;
    public const ALPHABETICAL_LEAF_FIRST = 2;
    public const ALPHABETICAL_LEAF_LAST = 3;

    /**
     * @param int $sortMode
     * @return array
     */
    public static function collection(int $sortMode = 0): array
    {
        $result = [
            'permissions' => config('guard.permissions', []),
        ];

        if ($sortMode !== self::NONE) {
            $iterator = function (array &$permission) use (&$iterator, $sortMode) {
                if (! empty($permission['permissions'])) {
                    $permission['permissions'] = collect($permission['permissions'])
                        ->sortBy(function (array $permission) use ($sortMode) {
                            if ($sortMode === self::ALPHABETICAL_LEAF_FIRST) {
                                return empty($permission['permissions']) ?
                                    "0-{$permission['title']}" :
                                    "1-{$permission['title']}";
                            } elseif ($sortMode === self::ALPHABETICAL_LEAF_LAST) {
                                return ! empty($permission['permissions']) ?
                                    "0-{$permission['title']}" :
                                    "1-{$permission['title']}";
                            }

                            return $permission['title'];
                        })
                        ->values()
                        ->toArray();

                    foreach ($permission['permissions'] as &$childPermission) {
                        $iterator($childPermission);
                    }
                }
            };

            $iterator($result);
        }

        return $result['permissions'];
    }

    /**
     * @return array
     */
    public static function leafOnly(): array
    {
        $iterator = function (array $permission, array &$state) use (&$iterator) {
            if (! empty($permission['permissions'])) {
                $childPermissions = collect($permission['permissions'])
                    ->sortBy('title')
                    ->values()
                    ->toArray();

                foreach ($childPermissions as $childPermission) {
                    $iterator($childPermission, $state);
                }
            } else {
                $state[$permission['name']] = $permission;
            }
        };

        $result = [];

        foreach (self::collection() as $permission) {
            $iterator($permission, $result);
        }

        return $result;
    }
}
