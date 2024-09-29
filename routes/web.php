<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TimeController;
use Illuminate\Support\Facades\Route;


Route::get('/', HomeController::class);

Route::post('appointment', [AppointmentController::class, 'store'])->name('appointments.store');

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => 'auth',
], function () {
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::put('appointments/{appointment}/update', [AppointmentController::class, 'update'])->name('appointments.update');

    Route::resource('services', ServiceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController:: class)->only(['store', 'update', 'destroy']);
    Route::resource('reviews', ReviewController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::group([
        'as' => 'times.',
        'prefix' => 'times',
        'controller' => TimeController::class,
    ], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/update', 'update')->name('update');
        Route::delete('/{time}/delete', 'delete')->name('destroy');
    });

});

Route::group([
    'controller' => AuthController::class
], function () {
    Route::inertia('/login', 'Auth/Login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest')->name('login');
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});
