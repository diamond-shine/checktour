<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\NotificationCollection;
use App\Tools\Meta\Message;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentUser = auth()->user();

        $time = Carbon::now()->sub(30, 'minutes')->format('Y-m-d H:i:s');

        $notifications = Notification::where('user_id', '=', $currentUser->id)
            ->where('created_at', '>', $time)
            ->get();

        return response()->json([
                "data" => [
                    'items' => new NotificationCollection($notifications)
                ],
                "meta" => []
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return [
            // 'meta' => [
            //     Message::make(_('Notification deleted'))->success(),
            // ],
        ];
    }
}
