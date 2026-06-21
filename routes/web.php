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

//  Guest-only (not logged in) 
Route::middleware('guest')->prefix('account')->name('account.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/create', [AuthController::class, 'showCreateAccount'])->name('create');
    Route::post('/create', [AuthController::class, 'createAccount']);

    Route::get('/create/seller', [AuthController::class, 'showCreateSeller'])->name('create.seller');
    Route::post('/create/seller', [AuthController::class, 'createSeller']);
});

//  Authenticated (check.user) 
Route::middleware('check.user')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');

    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/buyer', [DashboardController::class, 'buyer'])->name('buyer');
        Route::get('/seller', [DashboardController::class, 'seller'])->name('seller');
    });

    // Product
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/create', [ProductController::class, 'store']);
        Route::get('/edit', [ProductController::class, 'edit'])->name('edit');
        Route::post('/edit', [ProductController::class, 'update']);
        Route::get('/confirm_order', [OrderController::class, 'confirmOrder'])->name('confirm');
        Route::get('/view_receipt', [OrderController::class, 'viewReceipt'])->name('receipt');
        Route::get('/view', [ProductController::class, 'view'])->name('view');
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
});
