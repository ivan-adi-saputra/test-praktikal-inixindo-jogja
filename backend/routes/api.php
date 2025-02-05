<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json('Welcome to api');
});

Route::prefix('v1')->group(function () {
    Route::resource('/course', CourseController::class);
    Route::resource('/enrollment', EnrollmentController::class);
});
