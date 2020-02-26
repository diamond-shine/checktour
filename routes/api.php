<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('service/menu', 'ServiceController@menu')->name('service.menu');

require 'api/bookings.php';
require 'api/excursions.php';

require 'api/file_manager.php';
require 'api/notifications.php';
require 'api/rosters.php';
require 'api/schedules.php';
require 'api/tickets.php';
require 'api/tours.php';
require 'api/tour-options.php';
require 'api/users.php';

require 'api/webhooks.php';

Route::post('import/bookings', 'ServiceController@makeImport')
    ->middleware(['control.permission:import.bookings'])
    ->name('import.bookings');

Route::get('import/list', 'ServiceController@importList')
    ->middleware(['control.permission:import.bookings'])
    ->name('import.list');


Route::post('logout')
    ->name('control.auth.logout')
    ->uses(
        \App\Http\Controllers\Auth\LoginController::uses('logout')
    );
