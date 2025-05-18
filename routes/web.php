<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Account\AccountAdminController;
use App\Http\Controllers\Admin\Account\AccountUsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\admin\CategoryController;  // Tham chiếu đúng



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('/categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');

        Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('restore');
    });
 
    Route::prefix('/products')->name('products.')->group(function () {
        Route::get('/list', [ProductController::class, 'index'])->name('index');
        Route::get('/create-new', [ProductController::class, 'create'])->name('create');
        Route::post('/store-new', [ProductController::class, 'store'])->name('store');
        Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::get('/{id}/detail', [ProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/restore', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/{id}/forceDelete', [ProductController::class, 'forceDelete'])->name('forceDelete');
    });


    // Nhóm quản lý tài khoản
    Route::prefix('/account')->name('account.')->group(function () {
        // Users
        Route::get('/listUsers', [AccountUsersController::class, 'listUsers'])->name('listUsers');
        Route::get('/detailAccUser/{id}', [AccountUsersController::class, 'detailAccUser'])->name('detailAccUser');
        Route::get('/createUser', [AccountUsersController::class, 'createUser'])->name('createUser');
        Route::post('/storeUser', [AccountUsersController::class, 'storeUser'])->name('storeUser');
        Route::get('/editUser/{id}', [AccountUsersController::class, 'editUser'])->name('editUser');
        Route::post('/updateUser/{id}', [AccountUsersController::class, 'updateUser'])->name('updateUser');
        Route::post('/softDeleteUser/{id}', [AccountUsersController::class, 'softDeleteUser'])->name('softDeleteUser');
        Route::get('/trashedUsers', [AccountUsersController::class, 'trashedUsers'])->name('trashedUsers');
        Route::post('/restoreUser/{id}', [AccountUsersController::class, 'restoreUser'])->name('restoreUser');
        Route::delete('/forceDeleteUser/{id}', [AccountUsersController::class, 'forceDeleteUser'])->name('forceDeleteUser');
        Route::post('/resetPassword/{id}', [AccountUsersController::class, 'resetPassword'])->name('resetPassword');
        // Admins
        Route::get('/listAdmins', [AccountAdminController::class, 'listAdmins'])->name('listAdmins');
        Route::get('/detailAccAdmin/{id}', [AccountAdminController::class, 'detailAccAdmin'])->name('detailAccAdmin');
        Route::get('/createAdmin', [AccountAdminController::class, 'createAdmin'])->name('createAdmin');
        Route::post('/storeAdmin', [AccountAdminController::class, 'storeAdmin'])->name('storeAdmin');
        Route::get('/editAdmin/{id}', [AccountAdminController::class, 'editAdmin'])->name('editAdmin');
        Route::post('/updateAdmin/{id}', [AccountAdminController::class, 'updateAdmin'])->name('updateAdmin');
        Route::post('/softDeleteAdmin/{id}', [AccountAdminController::class, 'softDeleteAdmin'])->name('softDeleteAdmin');
        Route::get('/trashedAdmins', [AccountAdminController::class, 'trashedAdmins'])->name('trashedAdmins');
        Route::post('/restoreAdmin/{id}', [AccountAdminController::class, 'restoreAdmin'])->name('restoreAdmin');
        Route::delete('/forceDeleteAdmin/{id}', [AccountAdminController::class, 'forceDeleteAdmin'])->name('forceDeleteAdmin');
        Route::post('/resetPassword/{id}', [AccountAdminController::class, 'resetPassword'])->name('resetPassword');
    });
});

