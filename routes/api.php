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
    Route::post("/admin/users/add", [AdminController::class, "add_user"]);
    Route::post("/admin/users/change-password", [AdminController::class, "change_user_password"]);
    Route::post("/admin/users/delete", [AdminController::class, "delete_user"]);
    Route::post("/admin/users/update", [AdminController::class, "update_user"]);
    Route::post("/admin/users/fetch/{id}", [AdminController::class, "fetch_single_user"]);
    Route::post("/admin/users/fetch", [AdminController::class, "fetch_users"]);
    Route::post("/admin/fetch-settings", [AdminController::class, "fetch_settings"]);
    Route::post("/admin/save-settings", [AdminController::class, "save_settings"]);

    Route::post("/change-password", [UserController::class, "change_password"]);
    Route::post("/save-profile", [UserController::class, "save_profile"]);
    Route::post("/logout", [UserController::class, "logout"]);
    Route::post("/me", [UserController::class, "me"]);
});
