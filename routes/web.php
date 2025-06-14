<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\CommentController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\OrderStatusController;
use App\Http\Controllers\admin\PaymentMethodController;
use App\Http\Controllers\admin\AttributeValueController;
use App\Http\Controllers\admin\Product\ProductController;
use App\Http\Controllers\admin\Account\AccountAdminController;
use App\Http\Controllers\admin\Account\AccountUsersController;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\CategoryController;

use App\Http\Controllers\admin\Product\ProductVariantController;
use App\Http\Controllers\admin\ReviewController;

use App\Http\Controllers\Admin\BlogCategoryController;

use Dom\Comment;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login-submit', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');


    // Route::middleware(['admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý sản phẩm
    Route::prefix('/products')->name('products.')->group(function () {

        Route::get('/list', [ProductController::class, 'index'])->name('index');
        Route::get('/create-new', [ProductController::class, 'create'])->name('create');
        Route::post('/store-new', [ProductController::class, 'store'])->name('store');
        Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::get('/{slug}/detail', [ProductController::class, 'show'])->name('show');
        Route::get('/{slug}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{slug}/update', [ProductController::class, 'update'])->name('update');
        Route::delete('/{slug}/destroy', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{slug}/restore', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/{slug}/forceDelete', [ProductController::class, 'forceDelete'])->name('forceDelete');
        // Quản lý biến thể sản phẩm
        Route::prefix('/{product}/variants')->name('variants.')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/create-new', [ProductVariantController::class, 'create'])->name('create');
            Route::post('/store-new', [ProductVariantController::class, 'store'])->name('store');
            Route::get('/trashed', [ProductVariantController::class, 'trashed'])->name('trashed');
            Route::get('/{productVariant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('/{productVariant}/update', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('/{productVariant}/destroy', [ProductVariantController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/restore', [ProductVariantController::class, 'restore'])->name('restore');
        });
    });

    // Quản lý danh mục
    Route::prefix('/categories')->name('categories.')->group(function () {
        Route::get('/list', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{slug}/edit', [CategoryController::class, 'edit'])->name('edit'); // Sử dụng slug
        Route::put('/{slug}/update', [CategoryController::class, 'update'])->name('update'); // Sử dụng slug
        Route::delete('/{slug}/destroy', [CategoryController::class, 'destroy'])->name('destroy'); // Sử dụng slug
        Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
        Route::post('/{slug}/restore', [CategoryController::class, 'restore'])->name('restore'); // Sử dụng slug
        Route::delete('/{slug}/force-delete', [CategoryController::class, 'forceDelete'])->name('forceDelete'); // Sử dụng slug
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show'); // Show category details by slug
    });

    // quản lý blog_category
    Route::prefix('/blog-categories')->name('blog_categories.')->group(function () {
        Route::get('/list', [BlogCategoryController::class, 'index'])->name('index');
        Route::get('/create', [BlogCategoryController::class, 'create'])->name('create');
        Route::post('/store', [BlogCategoryController::class, 'store'])->name('store');
        Route::get('/{slug}/edit', [BlogCategoryController::class, 'edit'])->name('edit');
        Route::put('/{slug}/update', [BlogCategoryController::class, 'update'])->name('update');
        Route::delete('/{slug}/destroy', [BlogCategoryController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [BlogCategoryController::class, 'trash'])->name('trash');
        Route::post('/{slug}/restore', [BlogCategoryController::class, 'restore'])->name('restore');
        Route::delete('/{slug}/force_delete', [BlogCategoryController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/{slug}', [BlogCategoryController::class, 'show'])->name('show');
    });
    // bai viet
    Route::prefix('/blogs')->name('blogs.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/show/{id}', [BlogController::class, 'show'])->name('show');
        Route::get('/create', [BlogController::class, 'create'])->name(name: 'create');
        Route::get('/edit/{id}', action: [BlogController::class, 'edit'])->name('edit');
        Route::post('/store', [BlogController::class, 'store'])->name('store');
        Route::put('/store/{id}', [BlogController::class, 'update'])->name('update');
        Route::delete('/destroy', [BlogController::class, 'destroy'])->name('destroy');
    });
    // Quản lý sản phẩm
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
        // Quản lý biến thể sản phẩm
        Route::prefix('/{product}/variants')->name('variants.')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/create-new', [ProductVariantController::class, 'create'])->name('create');
            Route::post('/store-new', [ProductVariantController::class, 'store'])->name('store');
            Route::get('/trashed', [ProductVariantController::class, 'trashed'])->name('trashed');
            Route::get('/{productVariant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('/{productVariant}/update', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('/{productVariant}/destroy', [ProductVariantController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/restore', [ProductVariantController::class, 'restore'])->name('restore');
        });
    });


    // Nhóm quản lý tài khoản
    Route::prefix('/account')->name('account.')->group(function () {
        Route::prefix('/comment')->name('comment.')->group(function () {
            Route::get('/users/{user}/comments/trashed', [CommentController::class, 'getTrashedComments'])
                ->name('account.trashedComments');
            Route::post('/restore/{comment}', [CommentController::class, 'restoreCommentAjax'])->name('restoreComment');
            Route::post('/toggleStatus/{id}', [CommentController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/forceDelete/{id}', [CommentController::class, 'forceDelete'])->name('forceDelete');
            Route::get('/{comment}/details-with-product', [CommentController::class, 'getCommentDetailsWithProduct'])
                ->name('detailWithProduct');
            Route::post('/soft-delete/{comment}', [CommentController::class, 'softDeleteCommentAjax'])->name('softDeleteComment');

            Route::post('/approve/{comment}', [CommentController::class, 'approveCommentAjax'])->name('approveComment');
            Route::post('/hide/{comment}', [CommentController::class, 'hideCommentAjax'])->name('hideComment');
            Route::post('/show-again/{comment}', [CommentController::class, 'showAgainCommentAjax'])->name('showAgainComment');
        });
        // client
        Route::get('/listUsers', [AccountUsersController::class, 'listUsers'])->name('listUsers');
        Route::get('/detailAccUser/{id}', [AccountUsersController::class, 'detailAccUser'])->name('detailAccUser');
        Route::post('/softDeleteUser/{id}', [AccountUsersController::class, 'softDeleteUser'])->name('softDeleteUser');
        Route::get('/trashedUsers', [AccountUsersController::class, 'trashedUsers'])->name('trashedUsers');
        Route::post('/restoreUser/{id}', [AccountUsersController::class, 'restoreUser'])->name('restoreUser');
        Route::delete('/forceDeleteUser/{id}', [AccountUsersController::class, 'forceDeleteUser'])->name('forceDeleteUser');
        Route::post('/resetPassUser/{id}', [AccountUsersController::class, 'resetPassUser'])->name('resetPassUser');
        Route::get('/orders/{order}/ajax-details', [AccountUsersController::class, 'getAjaxOrderDetails'])
            ->name('order.ajaxDetails');
        // ROUTE MỚI CHO PHÂN QUYỀN
        Route::post('toggleUserRole/{user}', [AccountUsersController::class, 'toggleUserRole'])->name('toggleUserRole');
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
        // // ROUTE MỚI CHO PHÂN QUYỀN
        // Route::post('toggleUserRole/{admin}', [AccountAdminController::class, 'toggleUserRole'])->name('toggleUserRole');
    });



    //quản lí đánh giá 
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/create', [ReviewController::class, 'create'])->name('create');
        Route::post('/store', [ReviewController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [ReviewController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [ReviewController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [ReviewController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/{id}/show', [ReviewController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [ReviewController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/trash', [ReviewController::class, 'trash'])->name('trash');
    });

    //quản lí phương thức thanh toán
    Route::prefix('paymentMethods')->name('paymentMethods.')->group(function () {
        Route::get('/list', [PaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [PaymentMethodController::class, 'create'])->name('create');
        Route::post('/store', [PaymentMethodController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentMethodController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [PaymentMethodController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [PaymentMethodController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [PaymentMethodController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/{id}/show', [PaymentMethodController::class, 'show'])->name('show');
    });
    // Quản lý brands
    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/', [BrandController::class, 'store'])->name('store');
        Route::get('/trashed', [BrandController::class, 'trash'])->name('trash');
        Route::get('/{slug}', [BrandController::class, 'show'])->name('show');
        Route::get('/{slug}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{slug}', [BrandController::class, 'destroy'])->name('destroy');
        Route::post('/{slug}/restore', [BrandController::class, 'restore'])->name('restore');
        Route::delete('/{slug}/force-delete', [BrandController::class, 'forceDelete'])->name('forceDelete');
        Route::post('/bulk-delete', [BrandController::class, 'bulkSoftDelete'])->name('bulkSoftDelete');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::get('/trash', [CommentController::class, 'trash'])->name('trash');  // <-- đây
        Route::post('/approve', [CommentController::class, 'approve'])->name('approve');
        Route::post('/hide', [CommentController::class, 'hide'])->name('hide');
        Route::get('/{id}', [CommentController::class, 'show'])->name('show');
        Route::delete('/delete', [CommentController::class, 'destroy'])->name('destroy');
        Route::post('/restore/{id}', [CommentController::class, 'restore'])->name('restore');
        Route::delete('/force-delete', [CommentController::class, 'forceDelete'])->name('forceDelete');
        Route::post('/show-again', [CommentController::class, 'showAgain'])->name('showAgain');
    });

    // Quản lý banner
    Route::prefix('/banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}/update', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}/destroy', [BannerController::class, 'destroy'])->name('destroy');
    });
    // Quản lý thuộc tính sản phẩm
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

        // quản lý giá trị thuộc tính
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

    // Quản lý mã giảm giá
    Route::prefix('/discount')->name('discount.')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('index');
        Route::get('/create', [DiscountController::class, 'create'])->name('create');
        Route::get('/show/{id}', [DiscountController::class, 'show'])->name('show');
        Route::post('/store', [DiscountController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DiscountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DiscountController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DiscountController::class, 'destroy'])->name('delete');
        Route::get('/trash', [DiscountController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [DiscountController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [DiscountController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/history', [DiscountController::class, 'history'])->name('history');
        Route::get('/history/{id}', [DiscountController::class, 'historyDetail'])->name('historyDetail');
    });

    //quản lí phương thức thanh toán
    Route::prefix('paymentMethods')->name('paymentMethods.')->group(function () {
        Route::get('/list', [PaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [PaymentMethodController::class, 'create'])->name('create');
        Route::post('/store', [PaymentMethodController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentMethodController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [PaymentMethodController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [PaymentMethodController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [PaymentMethodController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/{id}/show', [PaymentMethodController::class, 'show'])->name('show');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::put('/{order}/updatePaymentStatus', [OrderController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');
        Route::delete('/destroy/{id}', [OrderController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [OrderController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [OrderController::class, 'restore'])->name('restore');
        Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');

    });
});
// });





