<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\ReasonController;
use App\Http\Controllers\admin\ColorController;
use App\Http\Controllers\admin\VehicleTypeController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\EmployeeTypeController;
use App\Http\Controllers\admin\EmployeeController;
use App\Http\Controllers\admin\ShiftController;
use App\Http\Controllers\admin\VacationController;
use App\Http\Controllers\admin\ContractController;
use Illuminate\Support\Facades\Route;


Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('brandmodels', BrandModelController::class)->names('admin.models');
Route::resource('reasons', ReasonController::class)->names('admin.reasons');
Route::resource('colors', ColorController::class)->names('admin.colors');
Route::resource('vehiclestypes', VehicleTypeController::class)->names('admin.vehiclestypes');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
Route::resource('employeetypes', EmployeeTypeController::class)->names('admin.employeetypes');
Route::resource('employees', EmployeeController::class)->names('admin.employees');
Route::resource('shifts', ShiftController::class)->names('admin.shifts');
Route::resource('contracts', ContractController::class)->names('admin.contracts'); 


Route::resource('vacations', VacationController::class)->names('admin.vacations');
Route::post('vacations/calculate-days', [VacationController::class, 'calculateDays'])->name('admin.vacations.calculate-days');
Route::post('vacations/check-available-days', [VacationController::class, 'checkAvailableDays'])->name('admin.vacations.check-available-days');
Route::post('vacations/{vacation}/change-status', [VacationController::class, 'changeStatus'])->name('admin.vacations.change-status');


Route::resource('/', AdminController::class)->names('admin');
