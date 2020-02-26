<?php

Route::prefix('webhook-listeners')->group(function () {
    Route::any('bookings-created', 'ImportWebhooks@bookingCreated')
        ->name('webhook-listeners.booking-created');

    Route::any('bookings-updated', 'ImportWebhooks@bookingUpdated')
        ->name('webhook-listeners.booking-updated');

    Route::any('bookings-deleted', 'ImportWebhooks@bookingDeleted')
        ->name('webhook-listeners.booking-deleted');
});
