<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandmodelController;
use App\Http\Controllers\admin\ReasonController;
use App\Http\Controllers\admin\ColorController;
use App\Http\Controllers\admin\VehicletypeController;
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
use App\Http\Controllers\admin\ChangeController;
use App\Http\Controllers\admin\MaintenanceController;
use App\Http\Controllers\admin\MaintenancerecordController;
use App\Http\Controllers\admin\MaintenancescheduleController;

use Illuminate\Support\Facades\Route;


Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('brandmodels', BrandmodelController::class)->names('admin.models');
Route::resource('reasons', ReasonController::class)->names('admin.reasons');
Route::resource('colors', ColorController::class)->names('admin.colors');
Route::resource('vehiclestypes', VehicletypeController::class)->names('admin.vehiclestypes');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
Route::resource('employees', EmployeeController::class)->names('admin.employees');
Route::resource('shifts', ShiftController::class)->names('admin.shifts');
Route::resource('contracts', ContractController::class)->names('admin.contracts');


Route::get('/employees/getposition/{id}', [EmployeeController::class, 'getPosition'])->name('admin.employees.getposition');


Route::resource('vacations', VacationController::class)->names('admin.vacations');

Route::post('vacations/check-available-days', [VacationController::class, 'checkAvailableDays'])->name('admin.vacations.check-available-days');
Route::post('vacations/calculate-days', [VacationController::class, 'calculateDays'])->name('admin.vacations.calculate-days');
Route::get('vacations/employee/{employeeId}/available-days', [VacationController::class, 'getEmployeeAvailableDays'])->name('admin.vacations.employee.available-days');
Route::patch('vacations/{vacation}/change-status', [VacationController::class, 'changeStatus'])->name('admin.vacations.change-status');

Route::resource('attendances', AttendanceController::class)->names('admin.attendances');
Route::resource('employeegroups', EmployeegroupController::class)->names('admin.employeegroups');
Route::resource('employee-types', EmployeeTypeController::class)->names('admin.employee-types');
Route::resource('changes', ChangeController::class)->names('admin.changes');
Route::resource('maintenance', MaintenanceController::class)->names('admin.maintenance');
Route::get('maintenanceschedule/{id}/getSchedule', [MaintenancescheduleController::class, 'getSchedule'])
    ->name('admin.maintenanceschedule.getSchedule');

Route::get('maintenanceschedule/create', [MaintenancescheduleController::class, 'create'])
    ->name('admin.maintenanceschedule.create');

Route::get('maintenanceschedule/{id}/edit', [MaintenancescheduleController::class, 'edit'])
    ->name('admin.maintenanceschedule.edit');

Route::post('maintenanceschedule/store', [MaintenancescheduleController::class, 'store'])
    ->name('admin.maintenanceschedule.store');

Route::put('maintenanceschedule/update/{id}', [MaintenancescheduleController::class, 'update'])
    ->name('admin.maintenanceschedule.update');

Route::delete('maintenanceschedule/destroy/{id}', [MaintenancescheduleController::class, 'destroy'])
    ->name('admin.maintenanceschedule.destroy');


Route::get('maintenancerecord/{id}/getSchedule', [MaintenancerecordController::class, 'getSchedule'])
    ->name('admin.maintenancerecord.getSchedule');

Route::get('maintenancerecord/create', [MaintenancerecordController::class, 'create'])
    ->name('admin.maintenancerecord.create');

Route::get('maintenancerecord/{id}/edit', [MaintenancerecordController::class, 'edit'])
    ->name('admin.maintenancerecord.edit');

Route::post('maintenancerecord/store', [MaintenancerecordController::class, 'store'])
    ->name('admin.maintenancerecord.store');

Route::put('maintenancerecord/update/{id}', [MaintenancerecordController::class, 'update'])
    ->name('admin.maintenancerecord.update');

Route::delete('maintenancerecord/destroy/{id}', [MaintenancerecordController::class, 'destroy'])
    ->name('admin.maintenancerecord.destroy');


Route::get('zones/map', [ZoneController::class, 'map'])->name('admin.zones.map');
Route::resource('zones', ZoneController::class)->names('admin.zones');
Route::get('data', [EmployeegroupController::class, 'data'])->name('admin.data');
Route::resource('schedulings', SchedulingController::class)->names('admin.schedulings');
Route::get('schedulings/get-content/{shiftId}', [SchedulingController::class, 'getContent'])->name('admin.schedulings.get-content');
Route::get('vehicles/by-type/{typeId}', [VehicleController::class, 'byType'])->name('admin.vehicles.bytype');
Route::get('employee-groups/vehiclechange/{group_id}', [EmployeegroupController::class, 'vehiclechange'])->name('admin.employee-groups.vehiclechange');
Route::put('employee-groups/vehiclechange/{group_id}', [EmployeegroupController::class, 'vehiclechangeUpdate'])->name('admin.employee-groups.vehiclechangeupdate');

Route::post('schedulings/add-change', [SchedulingController::class, 'AddChangeScheduling'])->name('admin.schedulings.add-change');
Route::get('schedulings/editModule/{id}', [SchedulingController::class, 'editModule'])->name('admin.schedulings.editModule');


Route::get('module', [SchedulingController::class, 'module'])->name('admin.module');
Route::get('createone', [SchedulingController::class, 'createOne'])->name('admin.schedulings.createOne');
Route::post('createone', [SchedulingController::class, 'storeOne'])->name('admin.schedulings.storeOne');
Route::get('validationVacations', [SchedulingController::class, 'validationVacations'])->name('admin.schedulings.validationVacations');
Route::get('module/data', [SchedulingController::class, 'getDatascheduling'])->name('admin.schedulings.getDatascheduling');
Route::resource('/', AdminController::class)->names('admin');
