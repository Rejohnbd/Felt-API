<?php

use App\Http\Controllers\Api\Admin\DeviceController;
use App\Http\Controllers\Api\Admin\DeviceTypeController;
use App\Http\Controllers\Api\Admin\ServicePackageController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\UserRoleController;
use App\Http\Controllers\Api\Admin\VehicleTypeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Customer\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/sanctum/token', [AuthController::class, 'sanctumAuth']);
Route::get('/sanctum/csrf-cookie', [AuthController::class, 'sanctumRevoke']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::get('/user/revoke', [AuthController::class, 'sanctumRevoke']);
    // Admin
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('all-users', [AuthController::class, 'allUsers']);
        Route::apiResource('device-types', DeviceTypeController::class);
        Route::apiResource('vehicle-types', VehicleTypeController::class);
        Route::apiResource('service-packages', ServicePackageController::class);
        Route::apiResource('devices', DeviceController::class);
        Route::apiResource('user-roles', UserRoleController::class);
        Route::apiResource('users', UserController::class);
        Route::post('users-update', [UserController::class, 'update']);
        Route::get('customer-users', [UserController::class, 'customerUsers']);
    });
    // Customer
    Route::prefix('customer')->middleware('customer')->group(function () {
        Route::get('profile', [DashboardController::class, 'getProfile']);
        Route::post('profile', [DashboardController::class, 'updateProfile']);
        Route::post('update-password', [DashboardController::class, 'updatePassword']);
    });
});
