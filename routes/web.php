<?php

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CartController;

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/product/details/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');

Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');

Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/item-remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');

Route::delete('/cart/item-empty', [CartController::class, 'empty_cart'])->name('cart.item.empty');



Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
});


Route::middleware(['auth', AuthAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('brands', [AdminController::class, 'brands'])->name('brands');
    Route::get('brand/create', [AdminController::class, 'brand_create'])->name('brand.create');
    Route::post('brand/store', [AdminController::class, 'brand_store'])->name('brand.store');
    Route::get('brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('brand.edit');
    Route::put('brand/update', [AdminController::class, 'brand_update'])->name('brand.update');
    Route::delete('brand/delete/{id}', [AdminController::class, 'brand_delete'])->name('brand.delete');


    Route::get('categories',[AdminController::class,'categories'])->name('categories');
    Route::get('category/create', [AdminController::class,'category_create'])->name('category.create');
    Route::post('category/store', [AdminController::class,'category_store'])->name('category.store');
    Route::get('category/edit/{id}', [AdminController::class, 'category_edit'])->name('category.edit');
    Route::put('category/update', [AdminController::class, 'category_update'])->name('category.update');
    Route::delete('category/delete/{id}', [AdminController::class, 'category_delete'])->name('category.delete');


    Route::get('products', [AdminController::class,'products'])->name('products');
    Route::get('product/create', [AdminController::class, 'product_create'])->name('product.create');
    Route::post('product/store', [AdminController::class, 'product_store'])->name('product.store');
    Route::get('product/edit/{id}', [AdminController::class, 'product_edit'])->name('product.edit');
    Route::put('product/update', [AdminController::class, 'product_update'])->name('product.update');
    Route::delete('product/delete/{id}', [AdminController::class, 'product_delete'])->name('product.delete');

    Route::get('coupons', [AdminController::class, 'coupons'])->name('coupons.index');
    Route::get('coupons/create', [AdminController::class, 'coupon_create'])->name('coupons.create');
    Route::post('coupons/store', [AdminController::class, 'coupon_store'])->name('coupon.store');
    Route::get('coupons/edit/{id}', [AdminController::class, 'coupon_edit'])->name('coupon.edit');
    Route::put('coupons/update', [AdminController::class, 'coupon_update'])->name('coupon.update');
    Route::delete('coupons/update/{id}', [AdminController::class, 'coupon_delete'])->name('coupon.delete');
});
