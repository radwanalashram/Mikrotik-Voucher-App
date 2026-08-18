<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherController;

Route::get('/', [VoucherController::class, 'create'])->name('vouchers.create');
Route::post('/vouchers/store', [VoucherController::class, 'store'])->name('vouchers.store');
Route::get('/vouchers/export-pdf', [VoucherController::class, 'exportPdf'])->name('vouchers.exportPdf');
