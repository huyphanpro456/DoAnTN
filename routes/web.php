<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\DeveloperController;
use App\Http\Controllers\AuthController;


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

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('tai-khoan',[DeveloperController::class,'showAccount'])->name('show-account');
    Route::put('tao',[DeveloperController::class,'account'])->name('account');

    Route::post('tim-kiem',[DeveloperController::class,'search'])->name('search');
    Route::resource('/cv',ProfileController::class);
});

Route::get('/', [DeveloperController::class, 'index'])->name('developer');

Route::prefix('employer')->group(function (){

    Route::get('login',[AuthController::class,'showFormLogin'])->name('show-login-emp');
    Route::post('login',[AuthController::class,'login'])->name('login-emp');

    Route::get('register',[AuthController::class,'showFormRegister'])->name('show-register-emp');
    Route::post('register',[AuthController::class,'register'])->name('register-emp');

});



