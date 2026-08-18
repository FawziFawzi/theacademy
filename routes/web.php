<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:system_admin')->group(function () {
        Route::resource('organizations', OrganizationController::class);
    });

    Route::middleware('role:system_admin,org_admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
        Route::resource('audit-logs', AuditLogController::class)->only(['index']);
    });

    Route::middleware('role:system_admin,org_admin,teacher')->group(function () {
        Route::resource('courses', CourseController::class)->only(['index', 'show']);
    });

    Route::middleware('role:system_admin,org_admin')->group(function () {
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::resource('plans', PlanController::class);
    });

    Route::middleware('role:org_admin,student')->group(function () {
        Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });
});

require __DIR__.'/auth.php';
