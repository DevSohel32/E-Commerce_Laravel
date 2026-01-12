<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;

Auth::routes();

Route::middleware(['auth'])->group(function(){
  Route::get('/account-dashboard', [UserController::class, 'index'])->name('home_index');
});
  Route::middleware(['auth',AuthAdmin::class])->group(function(){
  Route::get('/account-dashboard', ::class, 'index'])->name('home.index');
});


