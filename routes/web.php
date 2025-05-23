<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\admin\Product\ProductController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\admin\AttributeValueController;
use App\Http\Controllers\Admin\Account\AccountAdminController;
use App\Http\Controllers\Admin\Account\AccountUsersController;
use App\Http\Controllers\admin\CategoryController;  // Tham chiếu đúng
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\admin\Product\ProductVariantController;
use App\Http\Controllers\admin\OrderStatusController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('/categories')->name('categories.')->group(function () {
        Route::get('/list', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('forceDelete'); // ✅ THÊM DÒNG NÀY
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
        Route::prefix('/{product}/variants')->name('variants.')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/create-new', [ProductVariantController::class, 'create'])->name('create');
            Route::post('/store-new', [ProductVariantController::class, 'store'])->name('store');
            Route::get('/trashed', [ProductVariantController::class, 'trashed'])->name('trashed');
            Route::get('/{productVariant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('/{productVariant}/update', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('/{productVariant}/destroy', [ProductVariantController::class, 'destroy'])->name('destroy');
            Route::get('/{productVariant}/restore', [ProductVariantController::class, 'restore'])->name('restore');
        });
    });


    // Nhóm quản lý tài khoản
    Route::prefix('/account')->name('account.')->group(function () {
        // Users

        Route::prefix('/comment')->name('comment.')->group(function () {
            Route::get('/trashed', [CommentController::class, 'trashed'])->name('trashed');
            Route::post('/restore/{id}', [CommentController::class, 'restore'])->name('restore');
            Route::post('/toggleStatus/{id}', [CommentController::class, 'toggleStatus'])->name('toggleStatus');
            Route::post('/softDelete/{id}', [CommentController::class, 'softDelete'])->name('softDelete');
            Route::delete('/forceDelete/{id}', [CommentController::class, 'forceDelete'])->name('forceDelete');
        });

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
        Route::post('/resetPassUser/{id}', [AccountUsersController::class, 'resetPassUser'])->name('resetPassUser');
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
        Route::post('/resetPassAdmin/{id}', [AccountAdminController::class, 'resetPassAdmin'])->name('resetPassAdmin');
    });

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
        Route::post('/{id}/restore', [BrandController::class, 'restore'])->name('restore');
        Route::delete('/{id}/forceDelete', [BrandController::class, 'forceDelete'])->name('forceDelete');
    });
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    // Attribute
    Route::prefix('/attribute')->name('attribute.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::get('/trash', [AttributeController::class, 'trash'])->name('trash');
        Route::get('/show/{id}', [AttributeController::class, 'show'])->name('show');
        Route::get('/create', [AttributeController::class, 'create'])->name('create');
        Route::post('/store', [AttributeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AttributeController::class, 'edit'])->name('edit');
        Route::put('/{id}/update/', [AttributeController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy/', [AttributeController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/restore/', [AttributeController::class, 'restore'])->name('restore');
        // Attribute Value
        Route::prefix('/value')->name('value.')->group(function () {
            Route::get('/', [AttributeValueController::class, 'index'])->name('index');
            Route::get('/{id}/create/', [AttributeValueController::class, 'create'])->name('create');
            Route::post('/store', [AttributeValueController::class, 'store'])->name('store');
            Route::get('/{id}/edit/', [AttributeValueController::class, 'edit'])->name('edit');
            Route::put('/{id}/update/', [AttributeValueController::class, 'update'])->name('update');
            Route::delete('/{id}/destroy/', [AttributeValueController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/trash', [AttributeValueController::class, 'trash'])->name('trash');
            Route::patch('/{id}/restore/', [AttributeValueController::class, 'restore'])->name('restore');
        });
    });

    // discount
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
    Route::get('/discount/history', [DiscountController::class, 'history'])->name('discount.history');

    // Order status
    Route::prefix('/order')->name('order.')->group(function () {
        Route::prefix('/status')->name('status.')->group(function () {
            Route::get('/', [OrderStatusController::class, 'index'])->name('index');
            Route::get('/create', [OrderStatusController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [OrderStatusController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [OrderStatusController::class, 'update'])->name('update');
            Route::post('/store', [OrderStatusController::class, 'store'])->name('store');
        });
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
        Route::delete('/destroy/{id}', [OrderController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [OrderController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [OrderController::class, 'restore'])->name('restore');
    });
});
