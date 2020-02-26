<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\Controller;

Route::get('/')->name('dashboard.index')->uses(
    Controller::uses('index')
);

Route::get('control/login')
    ->name('control.auth.login')
    ->uses(
        \App\Http\Controllers\Auth\LoginController::uses('showLoginForm')
    );

Route::post('control/login')
    ->uses(
        \App\Http\Controllers\Auth\LoginController::uses('login')
    )->name('login-page');

Route::prefix('control/forgot')
    ->group(function () {
        Route::get('/reset')
            ->name('control.auth.password.request')
            ->uses(
                \App\Http\Controllers\Auth\ForgotPasswordController::uses('showLinkRequestForm')
            );

        Route::post('/email')
            ->name('control.auth.password.email')
            ->uses(
                \App\Http\Controllers\Auth\ForgotPasswordController::uses('sendResetLinkEmail')
            );

        Route::get('/reset/{token}')
            ->name('control.auth.password.reset')
            ->uses(
                \App\Http\Controllers\Auth\ResetPasswordController::uses('showResetForm')
            );

        Route::post('/reset')
            ->uses(
                \App\Http\Controllers\Auth\ResetPasswordController::uses('reset')
            );
    });

Route::prefix('invite/{inviteToken}')
    ->group(function () {
        Route::get('/')
            ->name('control.auth.invite')
            ->uses(
                \App\Http\Controllers\Auth\InviteController::uses('showInviteForm')
            );

        Route::post('/')
            ->uses(
                \App\Http\Controllers\Auth\InviteController::uses('process')
            );
    });


Route::get('/', 'DashboardController@index')->name('dashboard.index');
