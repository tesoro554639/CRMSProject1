<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/system-config', [SystemConfigController::class, 'index'])->name('system-config.index');
        Route::post('/system-config', [SystemConfigController::class, 'store'])->name('system-config.store');
    });

    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    });

    Route::middleware(['role:admin,manager,sales'])->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/approve', [CustomerController::class, 'approve'])->name('customers.approve');
        Route::post('customers/{customer}/reject', [CustomerController::class, 'reject'])->name('customers.reject');

        Route::get('leads/kanban/view', [LeadController::class, 'kanban'])->name('leads.kanban');
        Route::resource('leads', LeadController::class);
        Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
        Route::get('leads/{lead}/lost', [LeadController::class, 'lostForm'])->name('leads.lost-form');
        Route::patch('leads/{lead}/mark-lost', [LeadController::class, 'markLost'])->name('leads.mark-lost');
        Route::post('leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])->name('leads.convert');
        Route::post('leads/{lead}/reopen', [LeadController::class, 'reopen'])->name('leads.reopen');

        Route::resource('activities', ActivityController::class);

        Route::resource('follow-ups', FollowUpController::class);
        Route::post('follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
        Route::post('follow-ups/{followUp}/reopen', [FollowUpController::class, 'reopen'])->name('follow-ups.reopen');
    });
});
