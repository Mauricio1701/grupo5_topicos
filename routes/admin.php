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
use App\Http\Controllers\admin\AttendanceController;
use App\Http\Controllers\admin\EmployeegroupController;
use App\Http\Controllers\admin\ZoneController;
use App\Http\Controllers\admin\SchedulingController;
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


Route::get('/employees/getposition/{id}', [EmployeeController::class, 'getPosition'])->name('admin.employees.getposition');

Route::resource('vacations', VacationController::class)->names('admin.vacations');
Route::post('vacations/calculate-days', [VacationController::class, 'calculateDays'])->name('admin.vacations.calculate-days');
Route::post('vacations/check-available-days', [VacationController::class, 'checkAvailableDays'])->name('admin.vacations.check-available-days');
Route::post('vacations/{vacation}/change-status', [VacationController::class, 'changeStatus'])->name('admin.vacations.change-status');

Route::resource('attendances', AttendanceController::class)->names('admin.attendances');
Route::resource('employeegroups', EmployeegroupController::class)->names('admin.employeegroups');
Route::get('zones/map', [ZoneController::class, 'map'])->name('admin.zones.map');
Route::resource('zones', ZoneController::class)->names('admin.zones');
Route::get('data', [EmployeegroupController::class, 'data'])->name('admin.data');
Route::resource('schedulings', SchedulingController::class)->names('admin.schedulings');
Route::get('schedulings/get-content/{shiftId}', [SchedulingController::class, 'getContent'])->name('admin.schedulings.get-content');
Route::get('vehicles/by-type/{typeId}', [VehicleController::class, 'byType'])->name('admin.vehicles.bytype');
Route::get('employee-groups/vehiclechange/{group_id}', [EmployeegroupController::class, 'vehiclechange'])->name('admin.employee-groups.vehiclechange');
Route::put('employee-groups/vehiclechange/{group_id}', [EmployeegroupController::class, 'vehiclechangeUpdate'])->name('admin.employee-groups.vehiclechangeupdate');
Route::post('schedulings/add-change', [SchedulingController::class, 'AddChangeScheduling'])->name('admin.schedulings.add-change');
Route::get('module', [SchedulingController::class, 'module'])->name('admin.module');
Route::resource('/', AdminController::class)->names('admin');
