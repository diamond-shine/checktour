<?php

return [

    [
        'name' => 'schedules',
        'title' => _('Schedules'),
        'permissions' => [
            require __DIR__ . '/permission-parts/schedules.php',
        ],
    ],

    [
        'name' => 'tour-options',
        'title' => _('Tour-options'),
        'permissions' => [
            require __DIR__ . '/permission-parts/tour-options.php',
        ],
    ],

    [
        'name' => 'tours',
        'title' => _('Tours'),
        'permissions' => [
            require __DIR__ . '/permission-parts/tours.php',
        ],
    ],

    [
        'name' => 'manage',
        'title' => _('Manage'),
        'permissions' => [
            require __DIR__ . '/permission-parts/administration__users.php',

            require __DIR__ . '/permission-parts/administration__user-roles.php',

            require __DIR__ . '/permission-parts/administration__settings.php',
        ],
    ],
];
