<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', [ColocationController::class, 'index'])->middleware('auth')->name('homeColoc');
Route::post('/colocation', [ColocationController::class, 'Create'])->middleware('auth')->name('colocation.store');

Route::get('/dashboard', [UserController::class,'displayUsers'])
    ->middleware(['auth','ban'])
    ->name('dashboard');

Route::post('/users/{user}/toggle-ban', [UserController::class,'toggleBan'])->name('users.toggleBan')->middleware(['auth','admin','ban']);