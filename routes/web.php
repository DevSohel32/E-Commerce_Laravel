<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::middleware(['auth'])->prefix('user')->name('user.')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });


Route::middleware(['auth', AuthAdmin::class])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('brands', [AdminController::class, 'brands'])->name('brands');
        Route::get('brand/add',[AdminController::class,'add_brand'])->name('brand.add');
        Route::post('brand/store',[AdminController::class,'brand_store'])->name('brand.store');
        Route::get('brand/edit/{id}',[AdminController::class,'brand_edit'])->name('brand.edit');
        Route::put('brand/update', [AdminController::class, 'brands_update'])->name('brand.update');
    });


