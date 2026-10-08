<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// ---------- Julkinen sivusto ----------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/galleria', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/hinnasto', [PriceController::class, 'index'])->name('prices.index');
Route::get('/huoneet', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/huoneet/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/huoneet/{room}/arvostelu', [RoomController::class, 'storeReview'])
    ->middleware('throttle:6,1')->name('rooms.review');

Route::get('/palaute', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/palaute', [FeedbackController::class, 'store'])->middleware('throttle:6,1')->name('feedback.store');

// ---------- Varaus ----------
Route::get('/varaa', [BookingController::class, 'search'])->name('booking.search');
Route::get('/varaa/{room}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/varaa/{room}', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
Route::get('/varaus/{reference}', [BookingController::class, 'show'])->name('booking.show');

// Chatbot
Route::post('/chat', [ChatController::class, 'send'])->middleware('throttle:20,1')->name('chat.send');

// ---------- Jetstream-kirjautuneet ----------
Route::middleware('auth')->group(function () {
    Route::get('/omat-varaukset', [BookingController::class, 'mine'])->name('bookings.mine');
    Route::post('/varaus/{reference}/peruuta', [BookingController::class, 'cancel'])->name('booking.cancel');
    // Jetstream ohjaa kirjautumisen jälkeen reittiin "dashboard"
    Route::get('/dashboard', fn () => redirect()->route('bookings.mine'))->name('dashboard');
});

// ---------- Admin (salasanan takana) ----------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/kirjaudu', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/kirjaudu', [Admin\AuthController::class, 'login'])->name('login.submit');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::post('/ulos', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('rooms', Admin\RoomController::class)->except('show');
        Route::resource('gallery', Admin\GalleryController::class)->except('show');
        Route::resource('prices', Admin\PriceItemController::class)->except('show');

        Route::get('/varaukset', [Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::post('/varaukset/{booking}/peruuta', [Admin\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::delete('/varaukset/{booking}', [Admin\BookingController::class, 'destroy'])->name('bookings.destroy');

        Route::get('/palautteet', [Admin\FeedbackController::class, 'index'])->name('feedback.index');
        Route::delete('/palautteet/{feedback}', [Admin\FeedbackController::class, 'destroy'])->name('feedback.destroy');
        Route::delete('/arvostelut/{review}', [Admin\FeedbackController::class, 'destroyReview'])->name('reviews.destroy');
    });
});
