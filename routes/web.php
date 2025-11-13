<?php

use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\WeeklyMonthlyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Worker\ProfileController;
use App\Http\Controllers\Worker\TaskController as WorkerTaskController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function (): void {
    Route::get('/', fn() => redirect('/login'));

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // Task management
        Route::get('/tasks', (new AdminTaskController())->index(...))->name('tasks.index');
        Route::get('/tasks/calendar', (new AdminTaskController())->calendar(...))->name('tasks.calendar');
        Route::get('/tasks/{task}', (new AdminTaskController())->show(...))->name('tasks.show');
        Route::get('/tasks/{task}/edit', (new AdminTaskController())->edit(...))->name('tasks.edit');
        Route::post('/tasks', (new AdminTaskController())->store(...))->name('tasks.store');
        Route::patch('/tasks/{task}', (new AdminTaskController())->update(...))->name('tasks.update');
        Route::patch('/tasks/{task}/status', (new AdminTaskController())->updateStatus(...))->name('tasks.update-status');
        Route::delete('/tasks/{task}', (new AdminTaskController())->destroy(...))->name('tasks.destroy');
        Route::post('/tasks/bulk-delete', (new AdminTaskController())->bulkDelete(...))->name('tasks.bulk-delete');
        Route::post('/tasks/bulk-status', (new AdminTaskController())->bulkUpdateStatus(...))->name('tasks.bulk-status');
        Route::post('/tasks/bulk-assign', (new AdminTaskController())->bulkAssign(...))->name('tasks.bulk-assign');

        // Employee management
        Route::resource('employees', EmployeeController::class)->except(['show', 'create', 'edit']);
        Route::get('/employees/{employee}/performance', [EmployeeController::class, 'performance'])->name('employees.performance');

        // Weekly/Monthly task templates
        Route::get('/weekly-monthly', [WeeklyMonthlyController::class, 'index'])->name('weekly-monthly.index');
        Route::post('/weekly-monthly', [WeeklyMonthlyController::class, 'store'])->name('weekly-monthly.store');
        Route::delete('/weekly-monthly/{taskTemplate}', [WeeklyMonthlyController::class, 'destroy'])->name('weekly-monthly.destroy');

        // Equipment management
        Route::resource('equipment', EquipmentController::class);

        // Inventory management
        Route::resource('inventory', InventoryController::class)->except(['show']);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Assets (Lifts & Chillers)
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::post('/assets/lift', [AssetController::class, 'storeLift'])->name('assets.store-lift');
        Route::post('/assets/chiller', [AssetController::class, 'storeChiller'])->name('assets.store-chiller');
        Route::patch('/assets/lift/{lift}', [AssetController::class, 'updateLift'])->name('assets.update-lift');
        Route::patch('/assets/chiller/{chiller}', [AssetController::class, 'updateChiller'])->name('assets.update-chiller');
        Route::delete('/assets/lift/{lift}', [AssetController::class, 'destroyLift'])->name('assets.destroy-lift');
        Route::delete('/assets/chiller/{chiller}', [AssetController::class, 'destroyChiller'])->name('assets.destroy-chiller');

        // Profile management
        Route::get('/profile', (new AdminProfileController())->index(...))->name('profile.index');
        Route::patch('/profile', (new AdminProfileController())->update(...))->name('profile.update');
        Route::patch('/profile/password', (new AdminProfileController())->updatePassword(...))->name('profile.update-password');
    });

    // Worker routes
    Route::middleware('role:worker')->prefix('worker')->name('worker.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'workerDashboard'])->name('dashboard');

        // Tasks
        Route::get('/tasks', (new WorkerTaskController())->index(...))->name('tasks.index');
        Route::get('/schedule', (new WorkerTaskController())->schedule(...))->name('schedule');
        Route::patch('/tasks/{task}/status', (new WorkerTaskController())->updateStatus(...))->name('tasks.update-status');

        // Profile management
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
});
