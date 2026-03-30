<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiCartController;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\APICheckoutController;
use App\Http\Controllers\Api\ApiProductController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post("login", [ApiAuthController::class, "logIn"]);
Route::post("register", [ApiAuthController::class, "register"]);

// Products (public)
Route::get("categories", [ApiCategoryController::class, "categories"]);
Route::get("products", [ApiProductController::class, "index"]);
Route::get("products/{id}", [ApiProductController::class, "show"]);

// Protected routes
Route::middleware("auth:sanctum")->group(function () {

    Route::post("logout", [ApiAuthController::class, "logOut"]);
    Route::post("cart/add", [ApiCartController::class, "add"]);
    Route::post("checkout", [APICheckoutController::class, "checkout"]);
});
