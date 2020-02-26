<?php

Route::prefix('schedules')->group( function () {
    Route::get('list', 'ScheduleController@index')
        ->middleware('control.permission:schedules.list')
        ->name('schedules.list');

    Route::get('schedule-users', 'ScheduleController@scheduleUsers')
        ->middleware('control.permission:schedules.list')
        ->name('schedules.schedule-users');

    Route::get('view/{user}', 'ScheduleController@show')
        ->middleware('control.permission:schedules.view')
        ->name('schedules.view');

    Route::get('edit/{schedule}', 'ScheduleController@edit')
        ->middleware('control.permission:schedules.edit')
        ->name('schedules.edit');

    Route::post('create', 'ScheduleController@store')
        ->middleware('control.permission:schedules.create')
        ->name('schedules.create');

    Route::delete('delete/{schedule}', 'ScheduleController@destroy')
        ->middleware('control.permission:schedules.delete')
        ->name('schedules.destroy');


    // autocomplete
    Route::get('tours-autocomplete', 'ScheduleController@tours')
        ->middleware('control.permission:schedules.view')
        ->name('schedules.tours-autocomplete');

    Route::get('{tour}/users-autocomplete', 'ScheduleController@users')
        ->middleware('control.permission:schedules.view')
        ->name('schedules.users-autocomplete');

    Route::get('{tour}/date-autocomplete', 'ScheduleController@dateAutocomplete')
        ->middleware('control.permission:schedules.view')
        ->name('schedules.excursions-autocomplete');
});
