<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [ColocationController::class, 'index'])->name('home');
    Route::post('/colocation', [ColocationController::class, 'Create'])->name('colocation.store');
    Route::put('/colocation/{colocation}', [ColocationController::class, 'update'])->name('colocation.update');
    Route::delete('/colocation/{colocation}', [ColocationController::class, 'destroy'])->name('colocation.destroy');
    Route::post('/colocation/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocation.leave');
    Route::delete('/colocation/{colocation}/member/{userId}', [ColocationController::class, 'removeMember'])->name('colocation.removeMember');
    
    Route::post('/colocation/{colocation}/invite', [InvitationController::class, 'send'])->name('invitation.send');
    Route::get('/invitations/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');

    Route::post('/colocation/{colocation}/depense', [DepenseController::class, 'store'])->name('depense.store');
    Route::delete('/colocation/{colocation}/depense/{depense}', [DepenseController::class, 'destroy'])->name('depense.destroy');

    Route::post('/colocation/{colocation}/category', [CategoryController::class, 'store'])->name('category.store');
    Route::delete('/colocation/{colocation}/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});

Route::get('/dashboard', [UserController::class,'displayUsers'])
    ->middleware(['auth','ban'])
    ->name('dashboard');

Route::post('/users/{user}/toggle-ban', [UserController::class,'toggleBan'])->name('users.toggleBan')->middleware(['auth','admin','ban']);