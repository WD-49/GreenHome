<?php

use Dom\Comment;
use App\Jobs\SendTestMailJob;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PaymentController;
use Doctrine\DBAL\Schema\Index as DBALIndex;
use App\Http\Controllers\admin\BlogController;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\client\CartController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\Client\ShopController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\ReviewController;
use App\Http\Controllers\admin\CommentController;
use App\Http\Controllers\Admin\WebInfoController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\admin\CategoryController;

use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\Auth\AdminAuthController;

use App\Http\Controllers\Auth\SocialiteController;

use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\client\CheckoutController;
use App\Http\Controllers\client\WishlistController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\admin\OrderStatusController;
use App\Http\Controllers\client\BlogDetailController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\admin\PaymentMethodController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\admin\AttributeValueController;
use App\Http\Controllers\client\ProductClientController;
use App\Http\Controllers\admin\Product\ProductController;
use App\Http\Controllers\admin\Account\AccountAdminController;
use App\Http\Controllers\admin\Account\AccountUsersController;
use App\Http\Controllers\admin\Product\ProductVariantController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\client\BlogController as ClientBlogController;
use App\Http\Controllers\Client\DiscountController as ClientDiscountController;

Route::get('/test-log', function () {
    Log::debug('Testing logging functionality');
    return 'Log test triggered';
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes cho Google Login
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

// Routes cho Facebook Login
Route::get('/auth/facebook', [SocialiteController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialiteController::class, 'handleFacebookCallback']);

// route của trang client
// trang trủ
Route::get('/category/{id}', [HomeController::class, 'category'])->name('shop.category');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category-id/{id}', [ProductController::class, 'getProductsByCategoryId']);

//wishlist 
Route::middleware('auth')->prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/update-options', [WishlistController::class, 'updateOptions'])->name('wishlist.updateOptions');

// Route form gửi yêu cầu và xử lý của bạn
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot-password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'handle'])->name('forgot-password.handle'); // Sử dụng lại route này
// Các route đặt lại mật khẩu của Laravel
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{tab?}', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/{comment}/details-with-product', [CommentController::class, 'getCommentDetailsWithProduct'])
        ->name('detailWithProduct');
    Route::post('/profile/password-update', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    // Các route xác minh email CHUẨN
    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // *** THAY ĐỔI LỚN TẠI ĐÂY: DÙNG CONTROLLER THAY VÌ CLOSURE ***
    Route::post('/email/verification-notification', [EmailVerificationPromptController::class, 'sendVerificationEmail'])
        ->middleware(['throttle:6,1']) // Middleware throttle vẫn được giữ nguyên
        ->name('verification.send'); //throttle:6,1 giới hạn người dùng gửi yêu cầu xác thực email 6 lần trong 1 phút

    Route::post('/notifications/{id}/read', function ($id) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'read']);
    });
});

Route::get('/test-notify', function () {
    $user = \App\Models\User::find(9);
    $user->notify(new \App\Notifications\VerifyEmailReminder());
    return 'Notification sent';
});
// 

// route của trang admin
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // routes/api.php
    Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

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
        // ROUTE MỚI CHO PHÂN QUYỀN
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
        Route::post('/approve', [CommentController::class, 'approve'])->name('approve');
        Route::post('/hide', [CommentController::class, 'hide'])->name('hide');
        Route::get('/{id}', [CommentController::class, 'show'])->name('show');
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
        Route::get('/check-voucher-code', [DiscountController::class, 'checkCode'])->name('vouchers.checkCode');
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

        // Các route cập nhật trạng thái
        Route::put('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus'); // Đổi {id} thành {order} để nhất quán
        Route::put('/{order}/updatePaymentStatus', [OrderController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');

        Route::delete('/destroy/{id}', [OrderController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [OrderController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [OrderController::class, 'restore'])->name('restore');

        // Đề xuất dùng PUT/PATCH cho việc hủy đơn hàng để phù hợp hơn với ngữ nghĩa RESTful
        Route::put('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Hiển thị thông tin cấu hình website
    Route::get('/webinfor', [WebInfoController::class, 'show'])->name('web_info.show');
    Route::get('/webinfor/edit', [WebInfoController::class, 'edit'])->name('web_info.edit');
    Route::post('/webinfor/update', [WebInfoController::class, 'update'])->name('web_info.update');
});
// webinfor 

// route của trang client

// trang trủ
Route::get('/', [HomeController::class, 'index'])->name('home');
// routes/web.php
Route::get('/voucher/{code}/eligible-products', [ClientDiscountController::class, 'showEligibleProducts'])->name('voucher.products');
Route::get('/voucher/{code}/detail', [ClientDiscountController::class, 'showDetail'])->name('voucherDetail');


// viết tiếp route của các trang tại đây
Route::get('/blog/{slugCategory?}', [ClientBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/detail/{slug}', [ClientBlogController::class, 'show'])->name('blog.show');

Route::get('/san-pham/{slug}', [ProductClientController::class, 'show'])->name('productDetail');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

// giỏ hàng
route::prefix('cart')->middleware('auth')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'viewCart'])->name('view');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::post('/update-quantity/{id}', [CartController::class, 'updateQuantity'])->name('updateQuantity');
    Route::post('/delete-multiple', [CartController::class, 'deleteMultiple'])->name('deleteMultiple');
});

route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::get('/data', [CheckoutController::class, 'getCheckoutData'])->name('data');
    Route::post('/submit', [CheckoutController::class, 'submit'])->name('submit');
});

route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [CheckoutController::class, 'list'])->name('list');
    Route::get('/{order:sku}', [CheckoutController::class, 'show'])->name('show');
    Route::post('/cancel/{sku}', [CheckoutController::class, 'cancel'])->name('cancel');
    Route::get('/{order}/pay-again', [PaymentController::class, 'payAgain'])
        ->name('payAgain');
    Route::post('/{order}/confirm-received', [CheckoutController::class, 'confirmReceived'])
        ->name('confirmReceived');
});
Route::post('/review/submit', [CheckoutController::class, 'submitReview'])->name('client.review.submit');

// modalProduct
Route::get('/products/{id}', [ProductClientController::class, 'getProductDetails'])->name('product.details');



// web.php
Route::post('/checkout/payment', [PaymentController::class, 'createPaymentUrl'])->name('payment.vnpay');
Route::get('/payment/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');



Route::get('/blog/{slug}', [App\Http\Controllers\client\BlogDetailController::class, 'show'])->name('blog.detail'); // ? route gi day?

Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');


Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show'); // ? route gi day?


Route::post('/comment/submit', [ProductClientController::class, 'submitComment'])->name('client.comment.submit');
