<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Auth\Events\Attempting;
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

Route::middleware('auth')->group(function () {
    Route::get('/index', [AttendanceController::class, 'index']);
    Route::post('/clockin', [AttendanceController::class, 'clockIn']);
    Route::post('/clockout', [AttendanceController::class, 'clockOut']);
    Route::post('/breakstart', [AttendanceController::class, 'breakStart']);
    Route::post('/breakend', [AttendanceController::class, 'breakEnd']);
    //Route::post('/export', [ContactController::class, 'export']);
});

