<?php

use App\Http\Controllers\Api\Admin\DeviceController;
use App\Http\Controllers\Api\Admin\DeviceTypeController;
use App\Http\Controllers\Api\Admin\ServicePackageController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\UserRoleController;
use App\Http\Controllers\Api\Admin\VehicleController;
use App\Http\Controllers\Api\Admin\VehicleTypeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Customer\CustomerDriverController;
use App\Http\Controllers\Api\Customer\CustomerVhicleController;
use App\Http\Controllers\Api\Customer\CustomerVehicleDocument;
use App\Http\Controllers\Api\Customer\CustomerVehicleExpenseController;
use App\Http\Controllers\Api\Customer\CustomerVehicleReportController;
use App\Http\Controllers\Api\Customer\DashboardController;
use App\Http\Controllers\Api\DeviceDataController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\VehiclePaperController;

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
    // Common
    Route::apiResource('vehicle-papers', VehiclePaperController::class);
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
        Route::apiResource('vehicles', VehicleController::class);
    });
    // Customer
    Route::prefix('customer')->middleware('customer')->group(function () {
        Route::get('profile', [DashboardController::class, 'getProfile']);
        Route::post('profile', [DashboardController::class, 'updateProfile']);
        Route::post('update-password', [DashboardController::class, 'updatePassword']);
        Route::apiResource('customer-vehicles', CustomerVhicleController::class);
        Route::post('vehicle-update', [CustomerVhicleController::class, 'vehicleUpdate']);
        Route::get('customer-vehicles-speed-alert-setting', [CustomerVhicleController::class, 'vehicleSpeedSetting']);
        Route::get('customer-vehicles-speed-limitation/{id}', [CustomerVhicleController::class, 'speedLimitation']);
        Route::post('customer-vehicles-speed-limitation', [CustomerVhicleController::class, 'speedLimitationUpdate']);
        Route::get('customer-vehicles-alert-setting/{id}', [CustomerVhicleController::class, 'vehicleAlertSetting']);
        Route::post('customer-vehicles-alert-setting', [CustomerVhicleController::class, 'vehicleAlertSettingUpdate']);
        Route::apiResource('customer-drivers', CustomerDriverController::class);
        Route::apiResource('vehicle-documents', CustomerVehicleDocument::class);
        Route::apiResource('vehicle-expenses', CustomerVehicleExpenseController::class);
        Route::get('live-tracking', [CustomerVehicleReportController::class, 'liveTracking']);
    });
});

Route::post('device-data', [DeviceDataController::class, 'saveDeviceData']);
Route::post('request-data', [TestController::class, 'requestData']);
