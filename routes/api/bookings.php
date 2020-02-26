<?php

Route::prefix('bookings')->group( function () {
    Route::get('list', 'BookingController@index')
        ->middleware('control.permission:bookings.list')
        ->name('bookings.list');

    Route::get('list-forecasting', 'BookingController@listForecasting')
        ->middleware('control.permission:forecasting.list')
        ->name('bookings.forecasting');

    Route::get('list-waiting-room', 'BookingController@listWaitingRoom')
        ->middleware('control.permission:waiting-room.list')
        ->name('bookings.waiting-room');

    Route::get('list-rostered', 'BookingController@listRostered')
        ->middleware('control.permission:rostered.list')
        ->name('bookings.rostered');

    Route::get('list-processed', 'BookingController@listProcessed')
        ->middleware('control.permission:bookings.list')
        ->name('bookings.processed');

    Route::get('roster-bookings/{roster}', 'BookingController@rosterBookings')
        ->middleware('control.permission:rostered.list')
        ->name('bookings.roster-booking');



    Route::get('view/{booking}', 'BookingController@show')->name('bookings.view');
    Route::get('edit/{booking}', 'BookingController@edit')->name('bookings.edit');

    Route::get('create', 'BookingController@create')->name('bookings.create');

    Route::post('edit/{booking}', 'BookingController@update')->name('bookings.update');
    Route::post('process/{booking}', 'BookingController@process')->name('bookings.process');

    Route::post('create', 'BookingController@store')->name('bookings.create');


    // autocomplete
    Route::get('tours-autocomplete', 'BookingController@toursAutocomplete')
        ->middleware('control.permission:bookings.view')
        ->name('bookings.tours-autocomplete');

    Route::get('options-autocomplete/{tour}', 'BookingController@optionsAutocomplete')
        ->middleware('control.permission:bookings.view')
        ->name('bookings.options-autocomplete');

    Route::get('tickets-autocomplete/{tour}', 'BookingController@ticketsAutocomplete')
        ->middleware('control.permission:bookings.view')
        ->name('bookings.tickets-autocomplete');

    Route::get('autocomplete', 'BookingController@autocomplete')
        ->middleware('control.permission:bookings.process')
        ->name('bookings.autocomplete');
});
