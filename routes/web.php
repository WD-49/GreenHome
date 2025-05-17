<?php

use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\admin\AttributeValueController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/admin')->name('admin.')->group(function () {
    // Attribute
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/attribute', [AttributeController::class, 'index'])->name('attribute.index');
    Route::get('/attribute/trash', [AttributeController::class, 'trash'])->name('attribute.trash');
    Route::get('/attribute/show/{id}', [AttributeController::class, 'show'])->name('attribute.show');
    Route::get('/attribute/create', [AttributeController::class, 'create'])->name('attribute.create');
    Route::post('/attribute/store', [AttributeController::class, 'store'])->name('attribute.store');
    Route::get('/attribute/{id}/edit', [AttributeController::class, 'edit'])->name('attribute.edit');
    Route::put('/attribute/{id}/update/', [AttributeController::class, 'update'])->name('attribute.update');
    Route::delete('/attribute/{id}/destroy/', [AttributeController::class, 'destroy'])->name('attribute.destroy');
    Route::patch('/attribute/{id}/restore/', [AttributeController::class, 'restore'])->name('attribute.restore');
    // Attribute Value
    Route::get('/attribute/value/create/', [AttributeValueController::class, 'create'])->name('attribute.value.create');
    Route::get('/attribute/value/', [AttributeValueController::class, 'index'])->name('attribute.value.index');
    Route::post('/attribute/value/store', [AttributeValueController::class, 'store'])->name('attribute.value.store');
    Route::get('/attribute/value/{id}/edit/', [AttributeValueController::class, 'edit'])->name('attribute.value.edit');
    Route::put('/attribute/value/{id}/update/', [AttributeValueController::class, 'update'])->name('attribute.value.update');
    Route::delete('/attribute/value/{id}/destroy/', [AttributeValueController::class, 'destroy'])->name('attribute.value.destroy');
    Route::get('/attribute/value/trash', [AttributeValueController::class, 'trash'])->name('attribute.value.trash');
    Route::patch('/attribute/value/{id}/restore/', [AttributeValueController::class, 'restore'])->name('attribute.value.restore');
});