<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TimeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Index');
Route::resource('appointment', AppointmentController::class)->only('store');

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => 'auth',
], function () {
    Route::inertia('appointments', 'Admin/Appointments')->name('appointments');

    Route::resource('services', ServiceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController:: class)->only(['store', 'update', 'destroy']);
    Route::resource('reviews', ReviewController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('times', [TimeController::class, 'index'])->name('times.index');
    Route::post('times', [TimeController::class, 'store'])->name('times.store');
    Route::post('times/update', [TimeController::class, 'update'])->name('times.update');
    Route::delete('times/{time}/delete', [TimeController::class, 'delete'])->name('times.destroy');
});

Route::group([
    'controller' => AuthController::class
], function () {
    Route::inertia('/login', 'Auth/Login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest')->name('login');
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});
