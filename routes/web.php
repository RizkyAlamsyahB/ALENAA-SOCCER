<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('welcome');
// routes/web.php
Route::get('/maincourt', function () {
    return view('maincourt');
});
Route::get('/maincourt', function () {
    return view('maincourt');
});
Route::get('/membership', function () {
    return view('membership');
});
Route::get('/chat', function () {
    return view('chat');
});

Route::get('/product-rental', function () {
    return view('product-rental');
});
Route::get('/payment', function () {
    return view('payment');
});
Route::get('/mabar', function () {
    return view('mabar');
});
Route::get('/detail-mabar', function () {
    return view('detail-mabar');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';