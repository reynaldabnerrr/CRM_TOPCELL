<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PendingCustomerController;
use App\Http\Controllers\CustomerAfterCareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Sales Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/import', [SalesController::class, 'import'])->name('import');
        Route::post('/import', [SalesController::class, 'importStore'])->name('importStore');
        Route::get('/customer/{phoneNumber}', [SalesController::class, 'customerDetail'])->name('customer-detail');
        Route::post('/{id}/update-followup', [SalesController::class, 'updateFollowupStatus'])->name('update-followup');
        Route::get('/{sale}', [SalesController::class, 'show'])->name('show');
    });

    // Pending Customers Routes
    Route::prefix('pending-customers')->name('pending-customers.')->group(function () {
        Route::get('/', [PendingCustomerController::class, 'index'])->name('index');
        Route::get('/create', [PendingCustomerController::class, 'create'])->name('create');
        Route::post('/', [PendingCustomerController::class, 'store'])->name('store');
        Route::get('/{pendingCustomer}/edit', [PendingCustomerController::class, 'edit'])->name('edit');
        Route::patch('/{pendingCustomer}', [PendingCustomerController::class, 'update'])->name('update');
        Route::delete('/{pendingCustomer}', [PendingCustomerController::class, 'destroy'])->name('destroy');
    });

    // Aftercare Routes
    Route::prefix('aftercare')->name('aftercare.')->group(function () {
        Route::get('/', [CustomerAfterCareController::class, 'index'])->name('index');
        Route::get('/{aftercare}/edit', [CustomerAfterCareController::class, 'edit'])->name('edit');
        Route::patch('/{aftercare}', [CustomerAfterCareController::class, 'update'])->name('update');
        Route::patch('/{aftercare}/complete', [CustomerAfterCareController::class, 'markComplete'])->name('complete');
        Route::patch('/{aftercare}/skip', [CustomerAfterCareController::class, 'markSkipped'])->name('skip');
    });
});

require __DIR__.'/auth.php';
