<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Helpers\ImportBooking;
use App\Menus\Sidebar;
use App\Models\Import;
use App\Http\Resources\ImportCollection;
use App\Tools\Meta\Message;



class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu()
    {
        $user = auth()->user();

        $childrenItems = [
            Sidebar::dashboardBlock(),
            SideBar::bookingsBlock(),
        ];

        if ($user->can('schedules.list')) {
            $childrenItems[] = SideBar::sessionsBlock();
        }

        if ($user->can('tours.list') || $user->can('users.list')) {
            $childrenItems[] = SideBar::settingsBlock();
        }

        if ($user->isAdmin()) {
            $role = ['title' => _('Admin')];
        } else {
            $role = ['title' => implode(' / ', $user->roles->pluck('title')->toArray())];
        }

        return response()->json([
                "data" => [],
                "meta" => [
                [
                    "type" => "state",
                    "payload" => [
                        "sidebar" => [
                            "name" => "root",
                            "props" => [],
                            "children" => $childrenItems,
                        ],
                        "user" => [
                            "id" => $user->id,
                            "email" => $user->email,
                            "full_name" => $user->first_name . ' ' . $user->last_name,
                            "avatar" => $user->gravatar(),
                            "role" => $role,
                            "is_admin" => $user->isAdmin(),
                            'is_guide' => !$user->hasTourConciergeRole() && $user->hasGuideRole()
                        ],
                        "sites" => [],
                        "current_site" => null,
                        "site_url" => "https://signin.bookeo.com/"
                    ]
                ]
            ]
        ]);
    }

    public function makeImport(Request $request)
    {


        if ($last = Import::where('created_at', '>', Carbon::now()->sub(5, 'minute'))->first()) {
            return [
                'meta' => [
                    Message::make(_('Restriction period 5 minutes. Last attempt was at' .
                        ' '. $last->created_at ))->warning(),
                ]
            ];
        }

        $days = $request->get('days', 2);

        $startTime = Carbon::createMidnightDate()
            ->toIso8601String();

        $endTime = Carbon::now()->add($days, 'day')->toIso8601String();




        $import = Import::create([
            'user_id' => auth()->user()->id,
            'type' => 'manual'
        ]);

        $responceData = app('Bookeo')->getBookings([
            'startTime'       => $startTime,
            'endTime'         => $endTime,
            'expand_customer' => 'true'
        ]);

        $bookings = $responceData['data'] ?? $responceData['data'];

        $updated = 0;
        $created = 0;

        foreach ($bookings as $item) {
            if (!ImportBooking::handle($item)) {
                ImportBooking::handleUpdate($item);
                $updated++;
            } else {
                $created++;
            }
        }

        $import->fill([
            'updated' => $updated,
            'created' => $created,
            'status'  => 'completed'
        ])->save();

        return [
            'meta' => [
                Message::make(_('Import success'))->success(),
            ],
        ];
    }


    public function importList()
    {
        $importsList = Import::orderBy('id', 'DESC')->limit(20)->get();

        $importsList->load('user');

        return response()->json([
            "data" => [
                'items' => new ImportCollection($importsList)
            ]
        ]);
    }

}