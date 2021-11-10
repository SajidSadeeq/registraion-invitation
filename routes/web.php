<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::post('/confirm-code', [UserController::class, 'confirm_code'])->name('confirm_code');

Route::middleware(['auth'])->group(function () {
    Route::post('/update-profile', [UserController::class, 'update_profile']);
    Route::post('/invite', [UserController::class, 'invite_user']);
});

require __DIR__.'/auth.php';