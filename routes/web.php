<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandmodelController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/get-models/{brand_id}', [BrandModelController::class, 'getModelsByBrand'])->name('admin.getModelsByBrand');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('employee-types', App\Http\Controllers\Admin\EmployeeTypeController::class);
});

// Rutas para Turnos
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('shifts', App\Http\Controllers\Admin\ShiftController::class);
});

// Rutas para Empleados
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', App\Http\Controllers\Admin\EmployeeController::class);
});