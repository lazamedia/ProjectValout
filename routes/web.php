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
    return view('user',[
        "title" => "user",
        "active" => "user"
    ]);
});

// LOGIN LOGUOT
Route::get('/login', [LoginController::class, 'index' ] )->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate' ] );
Route::post('/logout', [LoginController::class, 'logout' ] );

Route::resource('/user', UserDashboardController::class)->middleware('auth');
Route::middleware(['auth'])->group(function () {
    
    // Rute untuk daftar proyek
    Route::get('/user', [UserDashboardController::class, 'index'])->name('user.index');

    // Rute untuk membuat proyek baru
    Route::get('/user/projects/create', [UserDashboardController::class, 'create'])->name('user.create');
    Route::post('/user/projects', [UserDashboardController::class, 'store'])->name('user.store');

    // Rute untuk mengedit proyek
    Route::get('/user/projects/{project}/edit', [UserDashboardController::class, 'edit'])->name('user.edit');
    Route::put('/user/projects/{project}', [UserDashboardController::class, 'update'])->name('user.projects.update');


    // Rute untuk menghapus proyek
    Route::delete('/user/projects/{project}', [UserDashboardController::class, 'destroy'])->name('user.destroy');

    // Rute untuk mengunduh semua file proyek
    Route::get('/user/projects/{project}/download', [UserDashboardController::class, 'download'])->name('user.projects.download');

    // Rute untuk bulk delete proyek
    Route::delete('user/bulk-delete', [UserDashboardController::class, 'bulkDelete'])->name('user.projects.bulkDelete');
});


Route::post('/register', [RegisterController::class, 'register'])->name('register');



Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin,super_admin'])->group(function () {
    
    Route::resource('/admin', AdminDasboardController::class);

});