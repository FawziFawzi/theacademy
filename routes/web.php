<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PaymentCheckoutController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'download'])->name('invoices.pdf');
        Route::resource('audit-logs', AuditLogController::class)->only(['index']);
    });

    Route::middleware('role:system_admin,org_admin,teacher')->group(function () {
        Route::resource('courses', CourseController::class)->only(['index', 'show'])->whereNumber('course');
    });

    Route::middleware('role:system_admin,org_admin')->group(function () {
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::resource('plans', PlanController::class);
    });

    // Read access: students see their portal, org admins their organization,
    // system admins everything (they have full access by design).
    Route::middleware('role:student,org_admin,system_admin')->group(function () {
        Route::resource('subscriptions', SubscriptionController::class)
            ->only(['index', 'show'])
            ->whereNumber('subscription');
    });

    // Subscribing is student-only: a system admin oversees subscriptions but
    // never becomes a subscriber himself.
    Route::middleware('role:student')->group(function () {
        Route::resource('subscriptions', SubscriptionController::class)
            ->only(['create', 'store'])
            ->whereNumber('subscription');

        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
            ->whereNumber('subscription')
            ->name('subscriptions.cancel');

        Route::post('payment/checkout/{plan}', [PaymentCheckoutController::class, 'checkout'])->name('payment.checkout');
        Route::get('payment/success', function () {
            return redirect()->route('subscriptions.index')->with('status', 'Payment completed. Webhook will confirm shortly.');
        })->name('payment.success');
        Route::get('payment/cancel', function () {
            return redirect()->route('subscriptions.index')->with('status', 'Payment canceled.');
        })->name('payment.cancel');
    });

    // Subscription management: system admins may edit or delete any subscription.
    Route::middleware('role:system_admin')->group(function () {
        Route::resource('subscriptions', SubscriptionController::class)
            ->only(['edit', 'update', 'destroy'])
            ->whereNumber('subscription');
    });
});

Route::post('payment/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');

require __DIR__.'/auth.php';
