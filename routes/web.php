<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::get('migrate/{key}', function ($key) {
    if ($key == 'Rejohn@333') {
        try {
            \Artisan::call('migrate');
            echo 'Migrated Successfully!';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    } else {
        echo 'Not matched!';
    }
});

Route::get('clear', function () {
    \Artisan::call('optimize:clear');
    \Artisan::call('cache:clear');
    \Artisan::call('config:cache');
    \Artisan::call('config:clear');
    echo "Run clear Successfully";
});
