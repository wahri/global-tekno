<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);

    Route::prefix('/cashier')->name('cashier.')->controller(CashierController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/addToCart', 'addToCart')->name('addToCart');
        Route::post('/scanCode', 'scanCode')->name('scanCode');
        Route::post('/submitOrder', 'submitOrder')->name('submitOrder');
        Route::put('/updateCart/{id}', 'updateCart')->name('updateCart');
        Route::delete('/removeFromCart/{id}', 'removeFromCart')->name('removeFromCart');
        Route::delete('/clearCart', 'clearCart')->name('clearCart');
    });

    Route::prefix('/restock')->name('restock.')->controller(RestockController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/addToCart', 'addToCart')->name('addToCart');
        Route::post('/scanCode', 'scanCode')->name('scanCode');
        Route::post('/submitOrder', 'submitOrder')->name('submitOrder');
        Route::put('/updateCart/{id}', 'updateCart')->name('updateCart');
        Route::delete('/removeFromCart/{id}', 'removeFromCart')->name('removeFromCart');
        Route::delete('/clearCart', 'clearCart')->name('clearCart');
    });

    Route::prefix('/report')->name('report.')->controller(ReportController::class)->group(function () {
        Route::get('/sales', 'sales')->name('sales');
        Route::delete('/sales/{id}', 'deleteSales')->name('sales.destroy');
        Route::get('/sale-items', 'saleItems')->name('saleItems');
        Route::get('/purchases', 'purchases')->name('purchases');
        Route::get('/purchase-items', 'purchaseItems')->name('purchaseItems');

    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
