<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'login')->name('login.page');
Route::post('/login', [AuthController::class, 'store'])->name('login');
