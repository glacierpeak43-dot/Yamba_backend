<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

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
Route::get('about', function(){
    return view('about');
});

Route::get('about', function(){
    return view('about');
});

Route::get('privacy-policy', function(){
    return view('privacy');
});

Route::get('eula', function(){
    return view('eula');
});

Route::get('delete-account-instructions', function(){
    return view('delete');
});

Route::get('refer',[\App\Http\Controllers\Auth\RegisterController::class, 'refer']);


Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
Route::get('/email/success', [VerificationController::class, 'verificationEmailSuccess'])->name('verification.success');
Route::get('/password/success', function () {
    return view('auth.verification-password-success');
})->name('password.success');
