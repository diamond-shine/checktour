<?php

namespace App\Menus;

use App\Models\Tour;


class Sidebar
{
    public static function bookingsBlock()
    {
        $items = [
            "name" => "Bookings",
            "props" => [],
            "children" => [
                [
                    "name" => "Forecasting",
                    "props" => [
                        "route" => [
                            "name" => "forecasting.list"
                        ],
                        "icon" => "fal fa-file-medical-alt"
                    ],
                    "children" => [],
                ],
                [
                    "name" => "Rostered",
                    "props" => [
                        "route" => [
                            "name" => "rostered.list"
                        ],
                        "icon" => "fas fa-user-tie"
                    ],
                    "children" => [],
                ],
                [
                    "name" => "Waiting room",
                    "props" => [
                        "route" => [
                            "name" => "waiting-room.list"
                            // "name" => "disabled"
                        ],
                        "icon" => "fal fa-hourglass"
                    ],
                    "children" => [],
                ],
            ],
        ];

        $user = app('shelter.auth')->user();

        if ($user->can('bookings.list')) {
            $items['children'][] = [
                "name" => "Processed",
                "props" => [
                    "route" => [
                        "name" => "processed.list"
                    ],
                    "icon" => "far fa-clipboard-check"
                ],
                "children" => [],
            ];

            $items['children'][] = [
                "name" => "All bookings",
                "props" => [
                    "route" => [
                        "name" => "bookings.list"
                    ],
                    "icon" => "far fa-book"
                ],
                "children" => [],
            ];
        }

        return $items;
    }

    public static function dashboardBlock()
    {
        return [
            "name" => "top",
            "props" => [],
            "children" => [
                [
                    "name" => "Rosters",
                    "props" => [
                        "route" => [
                            "name" => "rosters.list"
                        ],
                        "icon" => "fal fa-user-edit"
                    ],
                    "children" => [],
                ]
            ],
        ];
    }

    public static function sessionsBlock()
    {
        $menuBlock = [
            "name" => "Sessions",
            "props" => [],
            "children" => []
        ];

        $tours = Tour::query()->active()->get();

        foreach ($tours as $tour) {
            $menuBlock['children'][] = [
                "name" => $tour->name,
                "props" => [
                    "route" => [
                        "name" => "schedules.list",
                        "params" => [
                            "tourId" => $tour->id
                        ]
                    ],
                    "icon" => "fal fa-calendar-edit"
                ],
                "children" => []
            ];
        }

        return $menuBlock;
    }

    public static function settingsBlock()
    {
        $menuBlock = [
            "name" => "Manage",
            "props" => [],
            "children" => []
        ];

        $user = app('shelter.auth')->user();

        if ($user->can('tours.list')) {
            $menuBlock['children'][] = [
                "name" => "Tours",
                "props" => [
                    "route" => [
                        "name" => "tours.list"
                    ],
                    "icon" => "fal fa-landmark"
                ],
                "children" => []
            ];
        }

        if ($user->can('tours.list')) {
            $menuBlock['children'][] = [
                "name" => "Settings",
                "props" => [
                    "route" => [
                        "name" => "settings.edit"
                    ],
                    "icon" => "fal fa-cog"
                ],
                "children" => []
            ];
        }

        if ($user->can('users.list')) {
            $menuBlock['children'][] = [
                "name" => "Users",
                "props" => [
                    "route" => [
                        "name" => "users.list"
                    ],
                    "icon" => "icon c-icon-user"
                ],
                "children" => []
            ];
        }

        return $menuBlock;
    }
}
