<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
require __DIR__ . '/auth.php';

//Doctor routes

Route::apiResource('doctors', DoctorController::class, )->middleware('auth:sanctum');
Route::apiResource('schedules', ScheduleController::class, )->middleware('auth:sanctum');
Route::apiResource('patients', PatientController::class, )->middleware('auth:sanctum');

//patient api routes
