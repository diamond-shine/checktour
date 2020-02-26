<?php

Route::prefix('tours/{tour}/excursions')->group( function () {
    Route::get('list', 'ExcursionController@index')
        ->middleware('control.permission:excursions.list')
        ->name('excursions.list');

    Route::get('create', 'ExcursionController@create')
        ->middleware('control.permission:excursions.create')
        ->name('excursions.create');

    Route::post('create', 'ExcursionController@store')
        ->middleware('control.permission:excursions.create')
        ->name('excursions.create');

    // Route::post('edit/{excursion}', 'ExcursionController@update')->name('excursions.update');

    Route::delete('delete/{excursion}', 'ExcursionController@destroy')
        ->middleware('control.permission:excursions.delete')
        ->name('excursions.destroy');
});
