<?php

use App\Http\Controllers\AdminDasboardController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login',[
        "title" => "login",
        "active" => "login"
    ]);
})->middleware('guest');

Route::get('/register', function () {
    return view('auth.register',[
        "title" => "register",
        "active" => "register"
    ]);
});

Route::get('/home', function () {
    return view('home',[
        "title" => "home",
        "active" => "home"
    ]);
});

// LOGIN LOGUOT
Route::get('/login', [LoginController::class, 'index' ] )->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate' ] );
Route::post('/logout', [LoginController::class, 'logout' ] );

Route::resource('/user', UserDashboardController::class)->middleware('auth');
Route::delete('user/bulk-delete', [UserDashboardController::class, 'bulkDelete'])->name('user.bulkDelete')->middleware('auth');

Route::post('/register', [RegisterController::class, 'register'])->name('register');



Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin,super_admin'])->group(function () {
    
    Route::resource('/admin', AdminDasboardController::class);

});