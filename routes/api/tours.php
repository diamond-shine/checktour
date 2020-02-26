<?php

Route::prefix('tours')->group( function () {
    Route::get('list', 'TourController@index')
        ->middleware(['control.permission:tours.list'])
        ->name('tours.list');

    Route::get('view/{tour}', 'TourController@show')
        ->middleware(['control.permission:tours.view'])
        ->name('tours.view');

    Route::get('edit/{tour}', 'TourController@edit')
        ->middleware(['control.permission:tours.edit'])
        ->name('tours.edit');

    Route::post('edit/{tour}', 'TourController@update')
        ->middleware(['control.permission:tours.edit'])
        ->name('tours.update');

    Route::post('create', 'TourController@store')
        ->middleware(['control.permission:tours.create'])
        ->name('tours.create');

    // Route::delete('delete/{tour}', 'TourController@destroy')
    //     ->middleware(['control.permission:tours.delete'])
    //     ->name('tours.destroy');


    //users
    Route::get('users-autocomplete', 'TourController@usersAutocomplete')
        ->middleware(['control.permission:tours-users.edit'])
        ->name('tours.users');

    Route::get('users/{tour}/list', 'TourController@users')
        ->middleware(['control.permission:tours-users.view'])
        ->name('tours.users');

    //Route::get('users/{tour}/create', 'TourController@user')->name('ticket-options.create');
    Route::post('users/{tour}/create', 'TourController@attachUser')
        ->middleware(['control.permission:tours-users.edit'])
        ->name('tour-users.attach-user');

    Route::delete('users/{tour}/delete/{user}', 'TourController@detachUser')
        ->middleware(['control.permission:tours-users.edit'])
        ->name('tour-users.deteach-user');
});
