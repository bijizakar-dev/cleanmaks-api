<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\AbsenceController;
use App\Http\Controllers\API\CutiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Employee API
Route::prefix('employee')->middleware('auth:sanctum')->name('employee.')->group(function () {
    Route::get('', [EmployeeController::class, 'fetch'])->name('fetch');
    Route::post('', [EmployeeController::class, 'create'])->name('create');
    Route::post('update/{id}', [EmployeeController::class, 'update'])->name('update');
    Route::delete('{id}', [EmployeeController::class, 'destroy'])->name('delete');
});

// Absence API
Route::prefix('absence')->middleware('auth:sanctum')->name('absence.')->group(function () {
    Route::post('clock', [AbsenceController::class, 'clock'])->name('clock');
    Route::get('list', [AbsenceController::class, 'absenceList'])->name('list');
});

// Cuti API
Route::prefix('cuti')->middleware('auth:sanctum')->name('cuti.')->group(function () {
    Route::post('', [CutiController::class, 'create'])->name('create');
});

// Auth API
Route::name('auth.')->group(function () {
    Route::post('register', [UserController::class, 'register'])->name('register');
    Route::post('login', [UserController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('user', [UserController::class, 'fetch'])->name('fetch');
    });
});


