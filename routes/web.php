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

Route::get('/product/view', [ProductController::class, 'view'])->name('product.view');

//  Guest-only (not logged in) 
Route::middleware('guest')->group(function () {
    Route::get('/account/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/account/login', [AuthController::class, 'login']);

    Route::get('/account/create', [AuthController::class, 'showCreateAccount'])->name('register');
    Route::post('/account/create', [AuthController::class, 'createAccount']);

    Route::get('/account/create/seller', [AuthController::class, 'showCreateSeller'])->name('register.seller');
    Route::post('/account/create/seller', [AuthController::class, 'createSeller']);
});

//  Authenticated (check.user) 
Route::middleware('check.user')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard/buyer', [DashboardController::class, 'buyer'])->name('dashboard.buyer');
    Route::get('/dashboard/seller', [DashboardController::class, 'seller'])->name('dashboard.seller');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');

    // Orders
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
    Route::get('/product/confirm_order', [OrderController::class, 'confirmOrder'])->name('order.confirm');
    Route::get('/product/view_receipt', [OrderController::class, 'viewReceipt'])->name('order.receipt');

    // Products (authenticated: seller-gated in controller)
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/create', [ProductController::class, 'store']);
    Route::get('/product/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/edit', [ProductController::class, 'update']);

    // Profile
    Route::get('/edit/buyer', [ProfileController::class, 'editBuyer'])->name('profile.edit.buyer');
    Route::post('/edit/buyer', [ProfileController::class, 'updateBuyer']);
    Route::get('/edit/seller', [ProfileController::class, 'editSeller'])->name('profile.edit.seller');
    Route::post('/edit/seller', [ProfileController::class, 'updateSeller']);

    // Addresses
    Route::get('/edit/address', [ProfileController::class, 'editAddress'])->name('address.edit');
    Route::post('/edit/address', [ProfileController::class, 'updateAddress']);
    Route::get('/add/address', [ProfileController::class, 'addAddress'])->name('address.add');
    Route::post('/add/address', [ProfileController::class, 'storeAddress']);
});

//  Admin-only (check.admin) 
Route::middleware('check.admin')->prefix('dashboard/admin')->name('admin.')->group(function () {
    Route::get('/buyer', [AdminController::class, 'buyers'])->name('buyers');
    Route::get('/seller', [AdminController::class, 'sellers'])->name('sellers');
    Route::get('/order', [AdminController::class, 'orders'])->name('orders');
    Route::get('/application', [AdminController::class, 'applications'])->name('applications');
    Route::get('/product', [AdminController::class, 'products'])->name('products');
});
