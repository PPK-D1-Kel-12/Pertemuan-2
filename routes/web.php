<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Redirect Home to Tasks
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// Autentikasi (Login & Self-Registration)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Manajemen List (Novelya: SRS-F-03, SRS-F-04)
Route::prefix('lists')->name('lists.')->group(function () {
    Route::post('/', [ListController::class, 'store'])->name('store');
    Route::delete('/{list}', [ListController::class, 'destroy'])->name('destroy');
});

// Manajemen Task (Novelya, Joshua, Menza)
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    Route::post('/{task}/move-list', [TaskController::class, 'moveList'])->name('move-list');
    
    // Status & Tag Selesai (Joshua: SRS-F-07)
    Route::post('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
    
    // Fitur Inti Menza: Manajemen Kolaborator (SRS-F-08, SRS-F-09)
    Route::post('/{task}/collaborators', [TaskController::class, 'addCollaborator'])->name('collaborators.add');
    Route::delete('/{task}/collaborators/{user}', [TaskController::class, 'removeCollaborator'])->name('collaborators.remove');
    
    // Fitur Inti Menza: Pemantauan Progres & Linimasa (SRS-F-11)
    Route::post('/{task}/progress-notes', [TaskController::class, 'addProgressNote'])->name('progress-notes.add');
});

// Panel Administrasi (Iza: SRS-F-14, SRS-F-15, SRS-F-16)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});

// Demo Persona Switcher & Reset Mock Data
Route::get('/switch-user/{id}', [TaskController::class, 'switchUser'])->name('switch-user');
Route::post('/reset-data', [TaskController::class, 'resetData'])->name('reset-data');
