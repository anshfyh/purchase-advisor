<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/analyses/create', [AnalysisController::class, 'create'])
        ->name('analyses.create');

    Route::get('/history', [DashboardController::class, 'history'])->name('history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/analyses', [AnalysisController::class, 'store'])->name('analyses.store');

    Route::post('/analyses/ai-recommendation', [AnalysisController::class, 'aiRecommendation'])
        ->name('analyses.ai');

    Route::get('/analyses/{analysis}', [AnalysisController::class, 'show'])
        ->name('analyses.show');

    Route::delete('/analyses/{analysis}', [AnalysisController::class, 'destroy'])
        ->name('analyses.destroy');

    Route::patch('/analyses/{analysis}', [AnalysisController::class, 'update'])
        ->name('analyses.update');

    Route::post('/analyses/{analysis}/generate-ai', [AnalysisController::class, 'generateAi'])
        ->name('analyses.generate-ai');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])
        ->name('admin.users.role');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])
        ->name('admin.users.destroy');

    Route::get('/admin/analyses', [AdminController::class, 'analyses'])
        ->name('admin.analyses');

    Route::get('/admin/analyses/{analysis}', [AdminController::class, 'showAnalysis'])
        ->name('admin.analyses.show');

    Route::get('/admin/statistics', [AdminController::class, 'statistics'])
        ->name('admin.statistics');
});