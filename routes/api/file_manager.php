<?php

Route::prefix('file-manager')->group(function() {
    Route::prefix('explorer')->group(function() {
        Route::get('list/{folder?}')->uses(
            \Packages\FileManager\Http\Controllers\ExplorerController::uses('index')
        );

        Route::get('info/first-or-create-folder')->uses(
            \Packages\FileManager\Http\Controllers\ExplorerController::uses('firstOrCreateFolder')
        );
    });

    Route::post('uploads/upload/{folder?}')->uses(
        \Packages\FileManager\Http\Controllers\UploadsController::uses('upload')
    );
});