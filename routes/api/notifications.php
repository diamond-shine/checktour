<?php

Route::prefix('notifications')->group(function() {
    Route::get('list', 'NotificationController@index')
        ->middleware('control.permission:rosters.list')
        ->name('notifications.list');

    Route::delete('delete/{notification}', 'NotificationController@destroy')
        ->middleware('control.permission:rosters.list')
        ->name('notifications.destroy');
});