<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get("/payment/{user_id}/{course_id}", [App\Http\Controllers\api\PaymentController::class, 'index'])->name("payment");

Route::post("/payment/create",[App\Http\Controllers\api\PaymentController::class, 'makePayment'] )->name("makePayment");

Route::get("/payment/success", [App\Http\Controllers\api\PaymentController::class, 'success'])->name("payment.success");
