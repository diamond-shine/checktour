<?php

namespace App\Helpers;
use App\Models\TourOption;
use App\Models\Notification as NotificationModel;
use App\Models\Schedule;
use App\Models\User;

class Notification
{
    public static function roster(Schedule $roster, $message = null) {
        if (empty($message)) {
            $message = self::rosterMessage($roster);
        }

        $data = [
            'entity_id' => $roster->id,
            'entity_type' => get_class($roster),
            'message' => $message
        ];

        $users = self::rosterTargetUsers($roster);

        self::create($data, $users);
    }

    public static function option(TourOption $option, $message = null) {
        if (empty($message)) {
            $message = self::optionMessage($option);
        }

        $data = [
            'entity_id' => $option->id,
            'entity_type' => get_class($option),
            'message' => $message
        ];

        $users = self::optionTargetUsers($option);

        self::create($data, $users);
    }

    public static function booking($booking, $message = 'Booking was updated') {
        $currentUser = auth()->user();

        $schedule = $booking->schedule;

        if (!$schedule || $currentUser->id == $schedule->user_id) {
            return;
        }

        $data = [
            'user_id' => $schedule->user_id,
            'entity_id' => $booking->id,
            'entity_type' => get_class($booking),
            'message' => $message
        ];

        $notification = new NotificationModel($data);
        $notification->save();

    }

    private static function create($data, $users)
    {
        foreach ($users as $userId) {
            $data['user_id'] = $userId;
            $notification = new NotificationModel($data);
            $notification->save();
        }
    }


    private static function optionMessage(TourOption $option)
    {
        $currentUser = auth()->user();
        $dirty = $option->getDirty();
        $messages = [];

        $rosterTitle = $option->tour->name;

        if ($option->isDirty('is_active')) {
            $status = empty($dirty['is_active']) ? _('off') : _('on');
            $messages[] = $option->tour->name .' ' . _('option') . ' ' . $status;
        }

        return implode(" \n", $messages);
    }

    private static function rosterMessage(Schedule $roster)
    {
        $currentUser = auth()->user();
        $dirty = $roster->getDirty();
        $messages = [];

        $rosterTitle = $roster->user->fullName() . ' ' . $roster->excursion->time;

        if (!empty($dirty['is_enquired'])) {
            $messages[] = _('Roster') . ' ' . $rosterTitle . ' ' . _('enqire');
        }

        if (!empty($dirty['disabled_options']) && !$currentUser->hasGuideRole()) {
            $messages[] = _('Changed options') .' ' . _('for') . ' ' . _('roster') .' '. $rosterTitle;
        }

        if (!empty($dirty['is_recruited']) && !$currentUser->hasGuideRole()) {
            $messages[] = _('Roster') . ' ' . $rosterTitle . ' ' . _('was recruited');
        }

        return implode(" \n", $messages);
    }

    private static function optionTargetUsers($option)
    {
        $currentUser = auth()->user();
        $users = [];

        if ($option->isDirty('is_active')) {
            $idList = $option->tour->users()->where('id', '!=', $currentUser->id)
                ->get()
                ->pluck('id')
                ->toArray();

            $users = array_merge($users, $idList);


            $idList = User::whereHas('roles', function($query) {
                return $query->where('user_roles.name', '=', User::ROLE_TOUR_CONCIERGE);
            })
                ->where('id', '!=', $currentUser->id)
                ->get()
                ->pluck('id')
                ->toArray();

            $users = array_merge($users, $idList);
        }

        return array_unique($users);
    }

    private static function rosterTargetUsers($roster)
    {
        $currentUser = auth()->user();
        $dirty = $roster->getDirty();
        $users = [];

        if (!empty($dirty['is_recruited']) && !$currentUser->hasGuideRole()) {
            $users[] = $roster->user_id;
        }

        if (!empty($dirty['disabled_options']) && !$currentUser->hasGuideRole()) {
            $users[] = $roster->user_id;
        }

        if (!empty($dirty['is_enquired'])) {
            if ($currentUser->hasTourConciergeRole()) {
                $users[] = $roster->user_id;
            } else {
                $idList = User::whereHas('roles', function($query) {
                    return $query->where('name', User::ROLE_TOUR_CONCIERGE);
                })
                    ->get()
                    ->pluck('id')
                    ->toArray();

                $users = array_merge($users, $idList);
            }
        }

        return array_unique($users);
    }
}
