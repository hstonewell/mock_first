<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RegisterController;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::post('/clockin', [AttendanceController::class, 'clockIn']);
    Route::post('/clockout', [AttendanceController::class, 'clockOut']);
    Route::post('/breakstart', [AttendanceController::class, 'breakStart']);
    Route::post('/breakend', [AttendanceController::class, 'breakEnd']);
    Route::get('/attendance', [AttendanceController::class, 'viewAttendance'])->name('attendance.view');
    Route::post('/attendance/date', [AttendanceController::class, 'ViewByDate'])->name('attendance.date');
    Route::get('/user_attendance', [AttendanceController::class, 'viewUserAttendance'])->name('attendance.user');
    Route::get('/registered', [AttendanceController::class, 'viewRegistered']);
});

