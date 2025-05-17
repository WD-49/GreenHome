<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\Admin\BrandController;
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Brands
    Route::prefix('/brands')->name('brands.')->group(function () {
        Route::get('/list', [BrandController::class, 'index'])->name('index');
        Route::get('/create-new', [BrandController::class, 'create'])->name('create');
        Route::post('/store-new', [BrandController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [BrandController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [BrandController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [BrandController::class, 'destroy'])->name('destroy');

        // Thùng rác
        Route::get('/trashed', [BrandController::class, 'trash'])->name('trashed');
        Route::get('/{id}/restore', [BrandController::class, 'restore'])->name('restore');
        Route::delete('/{id}/forceDelete', [BrandController::class, 'forceDelete'])->name('forceDelete');
    });
});
