<?php

use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\LeccionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('/leccions', LeccionController::class);
Route::resource('/ejercicios', App\Http\Controllers\EjercicioController::class);
Route::resource('/cancions', App\Http\Controllers\CancionController::class);
