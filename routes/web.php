<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Auth::routes();

Route::middleware(['auth'])->group(function(){
  Route::get('/account-dashboard', [UserController::class, 'index'])->name('home.index');
});
  Route::middleware(['auth'])->group(function(){
  Route::get('/account-dashboard', [UserController::class, 'index'])->name('home.index');
});
  

