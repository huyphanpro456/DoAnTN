<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Developer\DeveloperController;
use App\Http\Controllers\Employer\EmployerController;
use App\Http\Controllers\Employer\RecruitmentController;
use App\Http\Controllers\Developer\ProfileController;
use App\Http\Controllers\Admin\AdminController;



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
    Route::get('cv/pdf/{id}',[ProfileController::class,'print_profile'])->name('print-pdf');
    Route::delete('delete-exp',[ProfileController::class,'deleteExp'])->name('delete-exp');

});

Route::get('/', [DeveloperController::class, 'index'])->name('developer');

Route::prefix('employer')->group(function (){

    Route::get('login',[AuthController::class,'showFormLogin'])->name('show-login-emp');
    Route::post('login',[AuthController::class,'login'])->name('login-emp');

    Route::get('register',[AuthController::class,'showFormRegister'])->name('show-register-emp');
    Route::post('register',[AuthController::class,'register'])->name('register-emp');

});

Route::prefix('adm')->group(function (){
    Route::middleware('admin')->group(function (){
        Route::get('/',[AdminController::class,'index'])->name('admin');
        Route::get('/tai-khoan-nguoi-tim-viec',[AdminController::class,'showUserDeveloper'])->name('show-user-developer');
        Route::get('/tai-khoan-nha-tuyen-dung',[AdminController::class,'showUserEmployer'])->name('show-user-employer');

        Route::get('/update-status',[AdminController::class,'updateStatus'])->name('update-status');
        Route::get('/tai-khoan-nha-tuyen-dung/{id}',[AdminController::class,'infoUserEmployer'])->name('info-user-employer');

        Route::get('/dang-thong-bao',[AdminController::class,'showPostNotice'])->name('show-post-notice');
        Route::post('/create-notice',[AdminController::class,'createNotice'])->name('create-notice');

        Route::get('logout',[AuthController::class,'logoutAdmin'])->name('logout-admin');

    });

    Route::get('login',[AuthController::class,'showFormLoginAdmin'])->name('show-login-admin');
    Route::post('login',[AuthController::class,'loginAdmin'])->name('login-admin');

});



