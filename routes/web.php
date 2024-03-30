<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Layanan\AbsenController;
use App\Http\Controllers\Layanan\CutiController;
use App\Http\Controllers\Layanan\IzinController;
use App\Http\Controllers\Masterdata\DivisiController;
use App\Http\Controllers\Masterdata\EmployeesController;
use App\Http\Controllers\Masterdata\JabatanController;
use App\Http\Controllers\Masterdata\JenisTypeController;
use App\Http\Controllers\Masterdata\UserController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('login', [AuthController::class, 'v_login'])->name('login');
Route::post('handleLogin', [AuthController::class, 'handleLogin'])->name('handleLogin');

Route::get('register', [AuthController::class, 'v_register'])->name('register');
Route::Post('handleRegister', [AuthController::class, 'handleRegister'])->name('handleRegister');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/setting-app', [SettingController::class, 'index'])->name('setting-app');
    Route::post('/setting-update', [SettingController::class, 'update'])->name('setting-update');

    Route::group(['middleware' => ['isLoginRoles:1']], function () {

        // ===================================== MASTERDATA ================================================

        // Pegawai
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeesController::class, 'index'])->name('index');
            Route::get('create', [EmployeesController::class, 'create'])->name('create');
            Route::post('store', [EmployeesController::class, 'store'])->name('store');
            Route::get('edit/{id}', [EmployeesController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [EmployeesController::class, 'update'])->name('update');
            Route::get('delete/{id}', [EmployeesController::class, 'destroy'])->name('delete');
        });

        // Divisi
        Route::prefix('divisi')->name('divisi.')->group(function () {
            Route::get('/', [DivisiController::class, 'index'])->name('index');
            Route::get('create', [DivisiController::class, 'create'])->name('create');
            Route::post('store', [DivisiController::class, 'store'])->name('store');
            Route::get('edit/{id}', [DivisiController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [DivisiController::class, 'update'])->name('update');
            Route::get('delete/{id}', [DivisiController::class, 'destroy'])->name('delete');
        });

        // Jabatan
        Route::prefix('jabatan')->name('jabatan.')->group(function () {
            Route::get('/', [JabatanController::class, 'index'])->name('index');
            Route::get('create', [JabatanController::class, 'create'])->name('create');
            Route::post('store', [JabatanController::class, 'store'])->name('store');
            Route::get('edit/{id}', [JabatanController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [JabatanController::class, 'update'])->name('update');
            Route::get('delete/{id}', [JabatanController::class, 'destroy'])->name('delete');
        });

        // User
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserController::class, 'update'])->name('update');
            Route::get('delete/{id}', [UserController::class, 'destroy'])->name('delete');
        });

         // Jenis Type
         Route::prefix('jenis-type')->name('jenis-type.')->group(function () {
            Route::get('/', [JenisTypeController::class, 'index'])->name('index');
            Route::get('create', [JenisTypeController::class, 'create'])->name('create');
            Route::post('store', [JenisTypeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [JenisTypeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [JenisTypeController::class, 'update'])->name('update');
            Route::get('delete/{id}', [JenisTypeController::class, 'destroy'])->name('delete');
        });


        // ===================================== LAYANAN ==================================================

        // Absen
        Route::prefix('absen')->name('absen.')->group(function () {
            Route::get('/', [AbsenController::class, 'index'])->name('index');
            Route::get('create', [AbsenController::class, 'create'])->name('create');
            Route::post('store', [AbsenController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AbsenController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AbsenController::class, 'update'])->name('update');
            Route::get('delete/{id}', [AbsenController::class, 'destroy'])->name('delete');

            Route::get('detail/{id}', [AbsenController::class, 'detail'])->name('detail');
            Route::post('edit_status/{id}', [AbsenController::class, 'edit_status'])->name('update-status');
        });

        // Izin
        Route::prefix('izin')->name('izin.')->group(function () {
            Route::get('/', [IzinController::class, 'index'])->name('index');
            Route::get('create', [IzinController::class, 'create'])->name('create');
            Route::post('store', [IzinController::class, 'store'])->name('store');
            Route::get('edit/{id}', [IzinController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [IzinController::class, 'update'])->name('update');
            Route::get('delete/{id}', [IzinController::class, 'destroy'])->name('delete');

            Route::get('detail/{id}', [IzinController::class, 'detail'])->name('detail');
            Route::post('edit_status/{id}', [IzinController::class, 'edit_status'])->name('update-status');
        });

        // Izin
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('create', [CutiController::class, 'create'])->name('create');
            Route::post('store', [CutiController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CutiController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CutiController::class, 'update'])->name('update');
            Route::get('delete/{id}', [CutiController::class, 'destroy'])->name('delete');

            Route::get('detail/{id}', [CutiController::class, 'detail'])->name('detail');
            Route::post('edit_status/{id}', [CutiController::class, 'edit_status'])->name('update-status');
            Route::get('checkCuti/{id}', [CutiController::class, 'checkCutiTahunan'])->name('check-cuti');
        });
    });
});
