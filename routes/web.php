<?php

use App\Http\Controllers\CheckoutDemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CheckoutDemoController::class, 'show'])->name('demo.checkout');
Route::post('/checkout', [CheckoutDemoController::class, 'submit'])->name('demo.checkout.submit');
Route::get('/payment/return', [CheckoutDemoController::class, 'paymentReturn'])->name('demo.payment.return');
