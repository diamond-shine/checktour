<?php

Route::prefix('tickets')->group( function () {
    Route::get('list', 'TicketController@index')
        ->middleware('control.permission:tickets.list')
        ->name('tickets.list');

    Route::get('view/{ticket}', 'TicketController@show')
        ->middleware('control.permission:tickets.view')
        ->name('tickets.view');

    Route::get('edit/{ticket}', 'TicketController@edit')
        ->middleware('control.permission:tickets.edit')
        ->name('tickets.edit');

    Route::post('edit/{ticket}', 'TicketController@update')
        ->middleware('control.permission:tickets.edit')
        ->name('tickets.update');

    Route::get('create', 'TicketController@create')
        ->middleware('control.permission:tickets.create')
        ->name('tickets.create');

    Route::post('create', 'TicketController@store')
        ->middleware('control.permission:tickets.create')
        ->name('tickets.create');

    Route::delete('delete/{ticket}', 'TicketController@destroy')
        ->middleware('control.permission:tickets.delete')
        ->name('tickets.destroy');



    // tickets options
    Route::get('{ticket}/options-autocomplete', 'TicketOptionController@autocomplete')
        ->middleware('control.permission:tickets.view')
        ->name('tickets.tour-options-autocomplete');

    Route::get('options/{ticket}/list', 'TicketOptionController@index')
        ->middleware('control.permission:tickets.view')
        ->name('ticket-options.list');

    Route::get('options/{ticket}/create', 'TicketOptionController@create')
        ->middleware('control.permission:tickets.edit')
        ->name('ticket-options.create');

    Route::post('options/{ticket}/create', 'TicketOptionController@store')
        ->middleware('control.permission:tickets.edit')
        ->name('ticket-options.create');

    // Route::post('options/{ticket}/edit/{ticketOption}', 'TicketOptionController@update')
    //     ->middleware('control.permission:tickets.edit')
    //     ->name('ticket-options.update');

    Route::delete('options/{ticket}/delete/{ticketOption}', 'TicketOptionController@destroy')
        ->middleware('control.permission:tickets.edit')
        ->name('ticket-options.destroy');
});
