<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/katalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{product:slug}', [ProductController::class, 'show'])->name('catalog.show');

require __DIR__ . '/admin.php';
