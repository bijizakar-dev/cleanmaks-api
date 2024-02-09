<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\AbsenceController;
use App\Http\Controllers\API\CutiController;
use App\Http\Controllers\API\AppController;
use App\Http\Controllers\API\PermitController;

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
    Route::get('history', [EmployeeController::class, 'employeeHistory'])->name('history');
});

// Absence API
Route::prefix('absence')->middleware('auth:sanctum')->name('absence.')->group(function () {
    Route::post('clock', [AbsenceController::class, 'clock'])->name('clock');
    Route::get('list', [AbsenceController::class, 'absenceList'])->name('list');

    Route::get('radiusAbsence', [AbsenceController::class, 'radiusAbsence'])->name('checkLocationAbsence');
    Route::get('checkQr', [AbsenceController::class, 'checkQr'])->name('checkQr');
});

// Cuti API
Route::prefix('cuti')->middleware('auth:sanctum')->name('cuti.')->group(function () {
    Route::get('', [CutiController::class, 'fetch'])->name('fetch');
    Route::post('', [CutiController::class, 'create'])->name('create');
});

// Permit API
Route::prefix('permit')->middleware('auth:sanctum')->name('permit.')->group(function () {
    Route::post('', [PermitController::class, 'create'])->name('create');
});

// Setting API
Route::prefix('setting')->middleware('auth:sanctum')->name('setting.')->group(function () {
    Route::get('', [AppController::class, 'getSetting'])->name('getSetting');
    Route::post('', [AppController::class, 'updateSetting'])->name('updateSetting');
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


