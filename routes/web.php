<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Index');
Route::resource('appointment', AppointmentController::class)->only('create', 'store');

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => 'auth',
], function () {
    Route::inertia('/appointments', 'Admin/Appointments')->name('appointments');

    Route::resource('services', ServiceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController:: class)->only(['store', 'update', 'destroy']);

    Route::inertia('/reviews', 'Admin/Reviews')->name('reviews');
    Route::inertia('/time', 'Admin/Time')->name('time');
});

Route::group([
    'controller' => AuthController::class
], function () {
    Route::inertia('/login', 'Auth/Login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest')->name('login');
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});
