<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masterdata\DivisiController;
use App\Http\Controllers\Masterdata\EmployeesController;
use App\Http\Controllers\Masterdata\JabatanController;
use App\Http\Controllers\Masterdata\UserController;
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

    Route::group(['middleware' => ['isLoginRoles:1']], function () {
        // Divisi
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
    });
});
