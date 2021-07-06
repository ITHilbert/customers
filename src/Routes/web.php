<?php

use Illuminate\Support\Facades\Route;
use ITHilbert\Customer\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(['web', 'auth'])
        ->group(function () {

    //Customer routes
    Route::prefix('customer')
        ->group(function () {
        Route::any('/',         [CustomerController::class, 'index']);
        Route::any('index',     [CustomerController::class, 'index'])->name('customer.index');
        Route::any('list',      [CustomerController::class, 'index'])->name('customer.list');
        Route::get('create',    [CustomerController::class, 'create'])->name('customer.create');
        Route::post('store',    [CustomerController::class, 'store'])->name('customer.store');

        Route::get('edit/{customerID}',         [CustomerController::class, 'edit'])->name('customer.edit');
        Route::post('update/{customerID}',      [CustomerController::class, 'update'])->name('customer.update');
        Route::delete('delete/{customerID}',    [CustomerController::class, 'delete'])->name('customer.delete');
        Route::get('/{customerID}',             [CustomerController::class, 'show'])->name('customer.show');
    });

});
