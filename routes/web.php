<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/dashboard', [UserController::class,'displayUsers'])
    ->middleware('auth')
    ->name('dashboard');


Route::post('/users/{user}/toggle-ban', [DashboardController::class,'toggleBan'])->name('users.toggleBan')->middleware('auth');