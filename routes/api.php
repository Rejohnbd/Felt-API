<?php

use App\Http\Controllers\Api\Admin\DeviceTypeController;
use App\Http\Controllers\Api\Admin\ServicePackageController;
use App\Http\Controllers\Api\Admin\VehicleTypeController;
use App\Http\Controllers\Api\Auth\AuthController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    // Admin
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('all-users', [AuthController::class, 'allUsers']);
        Route::apiResource('device-types', DeviceTypeController::class);
        Route::apiResource('vehicle-types', VehicleTypeController::class);
        Route::apiResource('service-packages', ServicePackageController::class);
    });
});
