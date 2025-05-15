<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\admin\CategoryController;  // Tham chiếu đúng



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Route cho thùng rác
    Route::get('categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::post('categories/restore/{id}', [CategoryController::class, 'restore'])->name('categories.restore');
});