<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

//  Public routes 
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::prefix('product')->name('product.')->group(function () {
    Route::get('/{product}/view', [ProductController::class, 'view'])->name('view');
});

// Dashboard
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/{buyer}/buyer', [DashboardController::class, 'buyer'])->name('buyer')->whereNumber('buyer');
    Route::get('/{seller}/seller', [DashboardController::class, 'seller'])->name('seller')->whereNumber('seller');
});

//  Guest-only (not logged in) 
Route::middleware('guest')->prefix('account')->name('account.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/create', [AuthController::class, 'showCreateAccount'])->name('create');
    Route::post('/create', [AuthController::class, 'createAccount']);
});

//  Authenticated (check.user) 
Route::middleware('check.user')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/create/seller', [AuthController::class, 'showCreateSeller'])->name('create.seller');
        Route::post('/create/seller', [AuthController::class, 'createSeller']);
    });

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::post('/cart/{cartItem}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');

    // Product (auth-only)
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/create', [ProductController::class, 'store']);
        Route::get('/confirm_order', [OrderController::class, 'confirmOrder'])->name('confirm');
        Route::post('/confirm_order', [OrderController::class, 'placeOrder'])->name('place');
        Route::get('/view_receipt', [OrderController::class, 'viewReceipt'])->name('receipt');

        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}/update', [ProductController::class, 'update'])->name('update');
        Route::post('/{product}/review', [ProductController::class, 'storeReview'])->name('review');
    });

    // Profile edits
    Route::prefix('edit')->name('edit.')->group(function () {
        Route::get('/buyer', [ProfileController::class, 'editBuyer'])->name('buyer');
        Route::post('/buyer', [ProfileController::class, 'updateBuyer']);
        Route::get('/seller', [ProfileController::class, 'editSeller'])->name('seller');
        Route::post('/seller', [ProfileController::class, 'updateSeller']);
        Route::get('/address', [ProfileController::class, 'editAddress'])->name('address');
        Route::post('/address', [ProfileController::class, 'updateAddress']);
    });

    // Addresses
    Route::prefix('add')->name('address.')->group(function () {
        Route::get('/address', [ProfileController::class, 'addAddress'])->name('add');
        Route::post('/address', [ProfileController::class, 'storeAddress']);
    });
});

//  Admin-only (check.admin) 
Route::middleware('check.admin')->prefix('dashboard/admin')->name('admin.')->group(function () {
    Route::get('/buyer', [AdminController::class, 'buyers'])->name('buyers');
    Route::get('/seller', [AdminController::class, 'sellers'])->name('sellers');
    Route::get('/order', [AdminController::class, 'orders'])->name('orders');
    Route::get('/application', [AdminController::class, 'applications'])->name('applications');
    Route::get('/product', [AdminController::class, 'products'])->name('products');

    // Actions
    Route::post('/buyer/{buyer}/toggle', [AdminController::class, 'toggleBuyer'])->name('buyer.toggle');
    Route::post('/seller/{seller}/toggle', [AdminController::class, 'toggleSeller'])->name('seller.toggle');
    Route::post('/product/{product}/toggle', [AdminController::class, 'toggleProduct'])->name('product.toggle');
    Route::post('/order/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('order.status');
    Route::post('/application/{application}/approve', [AdminController::class, 'approveApplication'])->name('application.approve');
    Route::post('/application/{application}/reject', [AdminController::class, 'rejectApplication'])->name('application.reject');
});
