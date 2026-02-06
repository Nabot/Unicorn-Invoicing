<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clients
    Route::resource('clients', ClientController::class);
    
    // Client Exports
    Route::get('clients/export/csv', [\App\Http\Controllers\ExportController::class, 'exportClientsCsv'])->name('clients.export.csv');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
    Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Payments
    Route::get('invoices/{invoice}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('invoices/{invoice}/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::patch('invoices/{invoice}/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('invoices/{invoice}/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Users
    Route::resource('users', UserController::class)->only(['index']);

    // Reports (hidden from navigation but routes still exist)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales-summary', [ReportController::class, 'salesSummary'])->name('reports.sales-summary');

    // Exports
    Route::get('invoices/export/csv', [\App\Http\Controllers\ExportController::class, 'exportCsv'])->name('invoices.export.csv');
    Route::get('invoices/export/pdf/batch', [\App\Http\Controllers\ExportController::class, 'exportPdfBatch'])->name('invoices.export.pdf.batch');
    Route::get('invoices/{invoice}/download-pdf', [\App\Http\Controllers\ExportController::class, 'exportPdf'])->name('invoices.download.pdf');
    Route::post('invoices/{invoice}/email', [\App\Http\Controllers\ExportController::class, 'emailInvoice'])->name('invoices.email');
});

require __DIR__.'/auth.php';
