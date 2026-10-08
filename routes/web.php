<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/yhteystiedot', function () {
    return view('yhteystiedot');
})->middleware(['auth'])->name('yhteystiedot');

Route::get('/info', function () {
    return view('info');
})->middleware(['auth'])->name('info');

Route::get('/palaute', function () {
    return view('palaute');
})->middleware(['auth'])->name('palaute');

Route::resource('todo', TodoController::class)->middleware('auth');
