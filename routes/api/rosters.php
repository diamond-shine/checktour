<?php

Route::prefix('rosters')->group(function() {
    Route::get('list', 'RosterController@index')
        ->middleware('control.permission:rosters.list')
        ->name('rosters.list');

    Route::get('view/{roster}', 'RosterController@show')
        ->middleware('control.permission:rosters.view')
        ->name('rosters.view');

    Route::post('edit/{roster}', 'RosterController@update')
        ->middleware('control.permission:rosters.update')
        ->name('roster.update');
});
