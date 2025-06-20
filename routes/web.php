<?php

use App\Http\Controllers\{
    CustomerController,
    DashboardController,
    ProductController,
    SalesController,
    PurchaseController,
    StockOpnameController,
    SupplierController
};
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('/users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
});

Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::get('/create', [SalesController::class, 'create'])->name('create');
    Route::get('/edit/{id}', [SalesController::class, 'edit'])->name('edit');
    Route::post('/', [SalesController::class, 'store'])->name('store');
    Route::put('/{id}', [SalesController::class, 'update'])->name('update');
    Route::get('/getProductCustomerPrices', [SalesController::class, 'getProductCustomerPrices'])->name('getProductCustomerPrices');

    Route::get('/{id}', [SalesController::class, 'detail'])->name('detail');
    Route::delete('/{id}', [SalesController::class, 'destroy'])->name('destroy');
});


Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
    Route::get('/edit/{id}', [PurchaseController::class, 'edit'])->name('edit');
    Route::post('/', [PurchaseController::class, 'store'])->name('store');
    Route::put('/{id}', [PurchaseController::class, 'update'])->name('update');
    Route::get('/getProduct', [PurchaseController::class, 'getProduct'])->name('getProduct');

    // Route::get('/{id}', [SalesController::class, 'detail'])->name('detail');
    Route::delete('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
});


Route::prefix('stock-opnames')->name('stock-opnames.')->group(function () {
    Route::get('/', [StockOpnameController::class, 'index'])->name('index');
});

Route::prefix('master-data/customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/ajaxGetCustomPrice', [CustomerController::class, 'ajaxGetCustomPrice'])->name('ajaxGetCustomPrice');

    Route::prefix('group')->name('group.')->group(function () {
        Route::get('/create', [CustomerController::class, 'createGroup'])->name('create');
        Route::get('/edit', [CustomerController::class, 'editGroup'])->name('edit');
        Route::post('/', [CustomerController::class, 'storeOrUpdateCustomerGroup'])->name('storeOrUpdate');
        Route::get('/{id}', [CustomerController::class, 'showCustomerGroup'])->name('show');
        Route::delete('/{id}', [CustomerController::class, 'deleteCustomerGroup'])->name('delete');
    });
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/create', [CustomerController::class, 'createPersonal'])->name('create');
        Route::post('/', [CustomerController::class, 'storeOrUpdateCustomerPersonal'])->name('storeOrUpdate');
        // Route::get('/{id}', [CustomerController::class, 'showCustomerGroup'])->name('show');
        Route::get('/{id}', [CustomerController::class, 'editPersonal'])->name('edit');
        Route::delete('/{id}', [CustomerController::class, 'deleteCustomerPersonal'])->name('delete');
    });
    Route::prefix('companies')->name('company.')->group(function () {
        Route::post('/', [CustomerController::class, 'storeOrUpdateCustomerCompany'])->name('storeOrUpdate');
        // Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::delete('/{id}', [CustomerController::class, 'deleteCustomerCompany'])->name('delete');
    });
    Route::prefix('outlets')->name('outlets.')->group(function () {
        Route::post('/', [CustomerController::class, 'storeOrUpdateCustomerOutlet'])->name('storeOrUpdate');
        // Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::delete('/{id}', [CustomerController::class, 'deleteCustomerOutlet'])->name('delete');
    });
    Route::prefix('custom_prices')->name('custom_prices.')->group(function () {
        Route::get('/', [CustomerController::class, 'insertInitCustomPrice'])->name('insertInitCustomPrice');
        Route::get('/updateSellingPrice', [CustomerController::class, 'updateSellingPrice'])->name('updateSellingPrice');
        Route::get('/restoreSellingPrice', [CustomerController::class, 'restoreSellingPrice'])->name('restoreSellingPrice');
        Route::get('/syncProducts', [CustomerController::class, 'syncProducts'])->name('syncProducts');
    });
});

Route::prefix('master-data/products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductController::class, 'delete'])->name('delete');
});
Route::prefix('master-data/suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
    Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{id}', [SupplierController::class, 'delete'])->name('delete');
});
