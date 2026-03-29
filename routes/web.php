<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AdminController;

// Auth routes
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',  [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Time Tracking (Employee)
    Route::post('/clock-in',  [TimeLogController::class, 'clockIn'])->name('clock.in');
    Route::post('/clock-out', [TimeLogController::class, 'clockOut'])->name('clock.out');
    Route::get('/my-logs',    [TimeLogController::class, 'myLogs'])->name('logs.my');
    Route::get('/my-logs/export', [TimeLogController::class, 'export'])->name('logs.export.my');

    // Logs (Admin + Superadmin)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/logs',        [TimeLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/export', [TimeLogController::class, 'export'])->name('logs.export');
    });

    // Employees (Admin + Superadmin)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/employees',              [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',       [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',             [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit',  [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}',       [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}',    [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Payroll (Admin + Superadmin)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/payroll',        [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/export', [PayrollController::class, 'export'])->name('payroll.export');
    });

    // Companies
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create',             [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies',                   [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit',     [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');
    });

    // Admins (Superadmin only)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admins',         [AdminController::class, 'index'])->name('admins.index');
        Route::get('/admins/create',  [AdminController::class, 'create'])->name('admins.create');
        Route::post('/admins',        [AdminController::class, 'store'])->name('admins.store');
        Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
    });
});
