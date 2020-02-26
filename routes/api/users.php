<?php

Route::prefix('users')->group(function () {
    Route::get('list', 'UserController@index')
        ->middleware(['control.permission:users.list']);

    Route::get('view/{user}', 'UserController@view')
        ->middleware(['control.permission:users.view']);

    Route::get('create', 'UserController@create')
        ->middleware(['control.permission:users.create']);

    Route::post('store', 'UserController@store')
        ->middleware(['control.permission:users.create']);

    Route::get('edit/{user}', 'UserController@edit')
        ->middleware(['control.permission:users.edit']);

    Route::post('update/{user}', 'UserController@update')
        ->middleware(['control.permission:users.edit']);

    Route::delete('delete/{user}', 'UserController@destroy')
        ->middleware(['control.permission:users.delete']);

    Route::get('invite', 'UserController@invite')
        ->middleware(['control.permission:users.invite']);

    Route::post('invite/send', 'UserController@sendInvite')
        ->middleware(['control.permission:users.invite']);
});
