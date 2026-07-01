<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page redirects to login if guest, dashboard if auth
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('organizations', OrganizationController::class)->except(['create', 'edit']);
    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::resource('employees', EmployeeController::class)->except(['create', 'edit']);
    Route::resource('payouts', PayoutController::class)->except(['create', 'edit']);
    Route::resource('roles', RoleController::class)->except(['show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
