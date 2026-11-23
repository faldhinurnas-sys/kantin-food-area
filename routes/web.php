<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\LimitController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\MidtransController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Midtrans Webhook (Tanpa CSRF)
Route::post('/midtrans/webhook', [MidtransController::class, 'webhook']);

// Profil & Cek Saldo NFC Routes (Semua pengguna terotentikasi)
Route::middleware('auth')->group(function () {
    Route::match(['post', 'put'], '/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/nfc/check-balance', [TransactionController::class, 'checkBalance'])->name('nfc.check_balance');
    Route::post('/topup/midtrans', [MidtransController::class, 'topup'])->name('topup.midtrans');
    Route::post('/midtrans/finish', [MidtransController::class, 'finishPayment'])->name('midtrans.finish');
    Route::post('/midtrans/cancel', [MidtransController::class, 'cancelPayment'])->name('midtrans.cancel');
});

// Pedagang Routes
Route::prefix('pedagang')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pedagang'])->name('pedagang.dashboard');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/transaction/store-order', [TransactionController::class, 'storeOrder'])->name('transaction.store_order');
    Route::post('/transaction/{id}/pay', [TransactionController::class, 'payOrder'])->name('transaction.pay_order');
    Route::post('/transaction/nfc', [TransactionController::class, 'processNfc'])->name('transaction.nfc');
    Route::put('/transaction/{id}/status', [TransactionController::class, 'updateOrderStatus'])->name('transaction.status');
});

// Mahasiswa Routes
Route::prefix('mahasiswa')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.dashboard');
    Route::post('/topup', [MidtransController::class, 'topup'])->name('mahasiswa.topup');
});

// Wali Routes
Route::prefix('wali')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wali'])->name('wali.dashboard');
    Route::post('/limit', [LimitController::class, 'store'])->name('limit.store');
    Route::post('/transfer', [TopupController::class, 'waliTransfer'])->name('wali.transfer');
    Route::get('/check-student-nim', [TopupController::class, 'checkStudentNim'])->name('wali.check_nim');
    Route::post('/topup-wallet', [MidtransController::class, 'topupWallet'])->name('wali.topup_wallet');
    Route::post('/topup-wallet/midtrans', [MidtransController::class, 'topupWallet'])->name('wali.topup_wallet.midtrans');
    Route::post('/topup', [MidtransController::class, 'topup'])->name('topup.store');
});

// Admin Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/users/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/users/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectUser'])->name('admin.users.reject');
    Route::put('/users/{id}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/cards', [\App\Http\Controllers\AdminController::class, 'storeCard'])->name('admin.cards.store');
    Route::post('/cards/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveCard'])->name('admin.cards.approve');
    Route::post('/cards/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectCard'])->name('admin.cards.reject');
    Route::post('/cards/{id}/replace', [\App\Http\Controllers\AdminController::class, 'replaceCard'])->name('admin.cards.replace');
    Route::delete('/cards/{id}', [\App\Http\Controllers\AdminController::class, 'destroyCard'])->name('admin.cards.destroy');
    Route::post('/limits/{id}/override', [\App\Http\Controllers\AdminController::class, 'overrideLimit'])->name('admin.limits.override');
    Route::get('/users/{id}/detail', [\App\Http\Controllers\AdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/users/{id}/rent', [\App\Http\Controllers\AdminController::class, 'updateRent'])->name('admin.users.rent');
});
