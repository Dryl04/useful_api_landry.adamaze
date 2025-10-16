<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;

Route::get('/', function () {
    return [
        "success" => true,
        "titre" => "useful_api_landry_adamaze",
        "version" => "1.0.0"    ];
});

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get("/modules", [ModuleController::class, "index"]);
    Route::post("/modules/{id}/activate", [ModuleController::class, "activate"]);
    Route::post("/modules/{id}/deactivate", [ModuleController::class, "deactivate"]);
});
