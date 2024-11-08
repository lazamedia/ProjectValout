<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminDasboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MandiriAbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserAbsensiController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/user');
});

Route::get('/register', function () {
    return view('auth.register',[
        "title" => "register",
        "active" => "register"
    ]);
});

Route::get('/absensi', function () {
    return view('absensi.index',[
        "title" => "absensi",
        "active" => "absensi"
    ]);
});

Route::get('/absensi/absen', function () {
    return view('absensi.absen',[
        "title" => "absensi",
        "active" => "absensi"
    ]);
});

Route::get('/absensi/create', function () {
    return view('absensi.create',[
        "title" => "absensi",
        "active" => "absensi"
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

    Route::delete('/projects/bulk-delete', [UserDashboardController::class, 'bulkDelete'])->name('user.projects.bulkDelete');


    // Rute untuk menampilkan form edit profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');

    // Rute untuk memproses pembaruan profil
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');


});


Route::post('/register', [RegisterController::class, 'register'])->name('register');



Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin,super_admin'])->group(function () {
    
    Route::resource('/admin', AdminDashboardController::class);
    // Rute untuk memeriksa data sebelum mengunduh
    Route::get('/admin/projects/check-download', [AdminDashboardController::class, 'checkDownload'])->name('admin.projects.checkDownload');

    // Rute untuk mengunduh semua proyek
    Route::get('/admin/projects/download-all', [AdminDashboardController::class, 'downloadAllProjects'])->name('admin.projects.downloadAll');    // Rute untuk mengedit proyek
    Route::get('/admin/projects/{project}/edit', [AdminDashboardController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/projects/{project}', [AdminDashboardController::class, 'update'])->name('admin.projects.update');


    // Rute untuk menghapus proyek
    Route::delete('/admin/projects/{project}', [AdminDashboardController::class, 'destroy'])->name('admin.destroy');


    // Route untuk menampilkan daftar pengguna
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

    // Route untuk memperbarui data pengguna via AJAX
    Route::post('/users/update', [AdminUserController::class, 'update'])->name('users.update');
    Route::resource('users', AdminUserController::class);


    Route::get('/absensi/{kode}', [AbsensiController::class, 'createAbsensi'])->name('absensi.createAbsensi');
    Route::post('/absensi/{kode}', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    Route::get('/absensi/mandiri', [AbsensiController::class, 'createAbsensi'])->name('absensi.mandiri');

    Route::resource('rooms', RoomController::class);
    // Route untuk menambahkan absensi via NIM (hanya admin)
    Route::post('/absensi/{room_id}/add', [AbsensiController::class, 'addByNim'])->name('absensi.addByNim');


    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Rute untuk menghapus massal pengguna
    Route::post('/users/bulk-destroy', [AdminUserController::class, 'bulkDestroy'])->name('users.bulkDestroy');


});



Route::middleware(['auth'])->group(function () {
    Route::get('/absenku', [UserAbsensiController::class, 'index'])->name('absenku.index');
    Route::post('/absenku/check', [UserAbsensiController::class, 'checkKode'])->name('absenku.check');
});