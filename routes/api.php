<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CompanyController;
use App\Http\Controllers\api\EmployeeController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);
Route::post("logout", [AuthController::class, "logout"])->middleware('auth:sanctum');


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::middleware('userMiddleware')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('companys', CompanyController::class);
        Route::apiResource('employees', EmployeeController::class);
    });
});
