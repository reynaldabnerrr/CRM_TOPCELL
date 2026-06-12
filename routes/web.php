<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PendingCustomerController;
use App\Http\Controllers\CustomerAfterCareController;
use App\Http\Controllers\AccountManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $today = \Carbon\Carbon::now()->toDateString();

    $followupToday = \App\Models\Sale::where(function ($query) use ($today) {
        $query->where(function ($q) use ($today) {
            $q->where('followup_h1_date', $today)->where('followup_h1_status', 'pending');
        })->orWhere(function ($q) use ($today) {
            $q->where('followup_h7_date', $today)->where('followup_h7_status', 'pending');
        })->orWhere(function ($q) use ($today) {
            $q->where('followup_1month_date', $today)->where('followup_1month_status', 'pending');
        });
    })->get();

    $overdueFollowups = \App\Models\Sale::where(function ($query) use ($today) {
        $query->where(function ($q) use ($today) {
            $q->where('followup_h1_date', '<', $today)->where('followup_h1_status', 'pending');
        })->orWhere(function ($q) use ($today) {
            $q->where('followup_h7_date', '<', $today)->where('followup_h7_status', 'pending');
        })->orWhere(function ($q) use ($today) {
            $q->where('followup_1month_date', '<', $today)->where('followup_1month_status', 'pending');
        });
    })->count();

    $stats = [
        'total_sales'           => \App\Models\Sale::count(),
        'today_followups'       => $followupToday->count(),
        'total_pending_customers' => \App\Models\PendingCustomer::count(),
        'pending_h1'            => \App\Models\Sale::where('followup_h1_status', 'pending')->count(),
        'pending_h7'            => \App\Models\Sale::where('followup_h7_status', 'pending')->count(),
        'pending_1month'        => \App\Models\Sale::where('followup_1month_status', 'pending')->count(),
        'overdue'               => $overdueFollowups,
        'done_total'            => \App\Models\Sale::where('followup_h1_status', 'done')
                                        ->orWhere('followup_h7_status', 'done')
                                        ->orWhere('followup_1month_status', 'done')->count(),
    ];

    return view('dashboard', compact('stats', 'followupToday'));
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
        Route::post('{invoice_number}/update-followup', [SalesController::class, 'updateFollowupStatus'])->name('update-followup')->where('invoice_number', '.*');
        Route::get('{sale:invoice_number}', [SalesController::class, 'show'])->name('show')->where('sale', '.*');
    });

    // Pending Customers Routes (Follow-up) - requires followup access
    Route::middleware('followup.access')->prefix('pending-customers')->name('pending-customers.')->group(function () {
        Route::get('/', [PendingCustomerController::class, 'index'])->name('index');
        Route::get('/followup', [PendingCustomerController::class, 'followup'])->name('followup');
        Route::get('/create', [PendingCustomerController::class, 'create'])->name('create');
        Route::post('/', [PendingCustomerController::class, 'store'])->name('store');
        Route::get('/{pendingCustomer}/edit', [PendingCustomerController::class, 'edit'])->name('edit');
        Route::patch('/{pendingCustomer}', [PendingCustomerController::class, 'update'])->name('update');
        Route::delete('/{pendingCustomer}', [PendingCustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{pendingCustomer}/update-followup-checkpoint', [PendingCustomerController::class, 'updateFollowupCheckpoint'])->name('update-followup-checkpoint');
        Route::delete('/statuses/{status}', [PendingCustomerController::class, 'destroyStatus'])->name('statuses.destroy');
    });

    // Aftercare Routes - requires aftercare access
    Route::middleware('aftercare.access')->prefix('aftercare')->name('aftercare.')->group(function () {
        Route::get('/', [CustomerAfterCareController::class, 'index'])->name('index');
        Route::patch('/{sale}/complete', [CustomerAfterCareController::class, 'markComplete'])->name('complete');
        Route::patch('/{sale}/skip', [CustomerAfterCareController::class, 'markSkipped'])->name('skip');
        Route::patch('/{sale}/pending', [CustomerAfterCareController::class, 'markPending'])->name('pending');
    });

    // Account Management Routes (superadmin only)
    Route::middleware('superadmin')->prefix('account-management')->name('account-management.')->group(function () {
        Route::get('/', [AccountManagementController::class, 'index'])->name('index');
        Route::post('/', [AccountManagementController::class, 'store'])->name('store');
        Route::delete('/{user}', [AccountManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-followup', [AccountManagementController::class, 'toggleFollowup'])->name('toggle-followup');
        Route::patch('/{user}/toggle-aftercare', [AccountManagementController::class, 'toggleAftercare'])->name('toggle-aftercare');
    });
});

require __DIR__.'/auth.php';
