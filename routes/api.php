<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/verify-email", [UserController::class, "verify_email"]);
Route::post("/reset-password", [UserController::class, "reset_password"]);
Route::post("/send-password-reset-link", [UserController::class, "send_password_reset_link"]);
Route::post("/login", [UserController::class, "login"]);
Route::post("/register", [UserController::class, "register"]);

Route::post("/admin/login", [AdminController::class, "login"]);

Route::group([
    "middleware" => ["auth:sanctum"]
], function () {
    Route::post("/admin/fetch-smtp-settings", [AdminController::class, "fetch_smtp_settings"]);
    Route::post("/admin/save-smtp-settings", [AdminController::class, "save_smtp_settings"]);

    Route::post("/change-password", [UserController::class, "change_password"]);
    Route::post("/save-profile", [UserController::class, "save_profile"]);
    Route::post("/logout", [UserController::class, "logout"]);
    Route::post("/me", [UserController::class, "me"]);
});
