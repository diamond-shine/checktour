<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Helpers\ImportBooking;
use App\Models\Import;

class ImportWebhooks extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bookingCreated(Request $request)
    {
        if ($itemId = $request->get('itemId', null)) {
            $responceData = app('Bookeo')->getBooking($itemId, ['expand_customer' => 'true']);

            $import = Import::create([
                'type' => 'Webhook'
            ]);

            $booking = ImportBooking::handle($responceData);

            $import->fill([
                'created' => 1,
                'updated' => 0,
                'status'  => 'completed'
            ])->save();

            return response(201);
        }

        return response(400);
    }

    public function bookingUpdated(Request $request)
    {
        if ($itemId = $request->get('itemId', null)) {
            $responceData = app('Bookeo')->getBooking($itemId, ['expand_customer' => 'true']);

            $import = Import::create([
                'type' => 'Webhook'
            ]);

            $booking = ImportBooking::handleUpdate($responceData);

            $import->fill([
                'updated' => 1,
                'created' => 0,
                'status'  => 'completed'
            ])->save();

            return response(201);
        }

        return response(400);
    }

    public function bookingDeleted(Request $request)
    {
        if ($itemId = $request->get('itemId', null)) {
            $responceData = app('Bookeo')->getBooking($itemId, ['expand_customer' => 'true']);
            $booking      = ImportBooking::handle($responceData);

            return response(201);
        }

        return response(400);
    }

}
