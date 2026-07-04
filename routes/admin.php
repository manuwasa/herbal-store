<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\TransactionHistoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('admin.login.store');
});

Route::post('/admin/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('admin.logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Owner-only: catalog, users, branches, settings.
    Route::middleware('role:owner')->group(function () {
        Route::resource('produk', ProductController::class)->except(['show'])->parameters(['produk' => 'product']);
        Route::resource('kategori', CategoryController::class)->except(['show'])->parameters(['kategori' => 'category']);
        Route::resource('pengguna', UserController::class)->except(['show'])->parameters(['pengguna' => 'user']);

        Route::post('/cabang/{branch}/set-default', [BranchController::class, 'setDefault'])->name('cabang.set-default');
        Route::resource('cabang', BranchController::class)->except(['show'])->parameters(['cabang' => 'branch']);

        Route::get('/pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    });

    // Reachable by both roles — data is scoped inside the controllers, not the route.
    Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{order}/invoice', [OrderController::class, 'invoice'])->name('pesanan.invoice');
    Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('pesanan.update');
    Route::put('/pesanan/{order}/cabang', [OrderController::class, 'reassignBranch'])->name('pesanan.reassign-branch');

    Route::get('/transfer-stok/massal/{branch}', [StockTransferController::class, 'bulkCreate'])->name('transfer-stok.bulk-create');
    Route::post('/transfer-stok/massal/{branch}', [StockTransferController::class, 'bulkStore'])->name('transfer-stok.bulk-store');
    Route::resource('transfer-stok', StockTransferController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['transfer-stok' => 'stockTransfer']);

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/riwayat-transaksi', [TransactionHistoryController::class, 'index'])->name('transactions.index');
});
