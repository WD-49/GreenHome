<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\admin\DiscountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/discount', [DiscountController::class, 'index'])->name('discount.index');
    Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');
    Route::get('/discount/show/{id}', [DiscountController::class, 'show'])->name('discount.show');
    Route::post('/discount/store', [DiscountController::class, 'store'])->name('discount.store');
    Route::get('/discount/{id}/edit', [DiscountController::class, 'edit'])->name('discount.edit');
    Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discount.update');
    Route::delete('/discount/delete/{id}', [DiscountController::class, 'destroy'])->name('discount.delete');
    Route::get('/discount/trash', [DiscountController::class, 'trash'])->name('discount.trash');
    Route::post('/discount/restore/{id}', [DiscountController::class, 'restore'])->name('discount.restore');
    Route::delete('/discount/force-delete/{id}', [DiscountController::class, 'forceDelete'])->name('discount.forceDelete');
});