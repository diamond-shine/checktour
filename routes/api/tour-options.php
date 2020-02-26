<?php

Route::get('tour-options/all', 'OptionController@all')
        ->middleware(['control.permission:tour-options.list'])
        ->name('tour-options.all');

Route::post('tour-options/manage', 'OptionController@manage')
        ->middleware('control.permission:tour-options.edit')
        ->name('tour-options.manage');

Route::prefix('tours/{tour}/tour-options')->group( function () {
    Route::get('list', 'OptionController@index')
        ->middleware(['control.permission:tour-options.list'])
        ->name('tour-options.list');

    Route::get('view/{tourOption}', 'OptionController@show')
        ->middleware('control.permission:tour-options.view')
        ->name('tour-options.view');

    Route::get('create', 'OptionController@create')
        ->middleware('control.permission:tour-options.create')
        ->name('tour-options.create');

    Route::post('create', 'OptionController@store')
        ->middleware('control.permission:tour-options.create')
        ->name('tour-options.create');

    Route::get('edit/{tourOption}', 'OptionController@edit')
        ->middleware('control.permission:tour-options.edit')
        ->name('tour-options.edit');

    Route::post('edit/{tourOption}', 'OptionController@update')
        ->middleware('control.permission:tour-options.edit')
        ->name('tour-options.update');

    Route::delete('delete/{tourOption}', 'OptionController@destroy')
        ->middleware('control.permission:tour-options.edit')
        ->name('tour-options.destroy');
});
