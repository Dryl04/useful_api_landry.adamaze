<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ShortLinkController;

Route::get('/', function () {
    return [
        "success" => true,
        "titre" => "useful_api_landry_adamaze",
        "version" => "1.0.0"    ];
});

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"])->name('login');

Route::get("/s/{code}", [ShortLinkController::class, "redirect"]);

// Routes Module 0
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get("/modules", [ModuleController::class, "index"]);
    Route::post("/modules/{id}/activate", [ModuleController::class, "activate"]);
    Route::post("/modules/{id}/deactivate", [ModuleController::class, "deactivate"]);
});

// Routes Module 1
Route::middleware(['auth:sanctum', 'module.active:URL Shortener'])->group(function () {
    Route::post("/shorten", [ShortLinkController::class, "shorten"]);
    Route::get("/links", [ShortLinkController::class, "index"]);
    Route::delete("/links/{id}", [ShortLinkController::class, "destroy"]);
});
