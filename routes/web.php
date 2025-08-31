<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\EmployerController;
use App\Http\Controllers\Backend\JobSeekerController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return view('login');
})->name('login');


use App\Http\Controllers\PaymentController;

Route::get('/checkout', [PaymentController::class, 'checkout']);
Route::post('/jobs/{id}/pay', [PaymentController::class, 'pay'])->name('payment.pay');
Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::post('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

