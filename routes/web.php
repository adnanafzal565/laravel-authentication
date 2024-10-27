<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

use App\Http\Middleware\User;
use App\Http\Middleware\Admin;

Route::group([
    "middleware" => [User::class]
], function () {
    Route::post("/logout", [UserController::class, "do_logout"]);
    Route::get("/email-verification/{email}", [UserController::class, "email_verification"]);
    Route::get("/reset-password/{email}/{token}", [UserController::class, "reset_password_view"])
        ->name("password.reset");
    Route::get("/forgot-password", [UserController::class, "forgot_password"])
        ->name("password.request");
    Route::get("/register", [UserController::class, "register_view"]);
    Route::get("/login", [UserController::class, "login_view"]);
    Route::get("/change-password", [UserController::class, "change_password_view"]);
    Route::get("/profile", [UserController::class, "profile"]);
    Route::get("/", [UserController::class, "home"]);
});

Route::post("/login", [UserController::class, "do_login"]);

Route::group([
    "middleware" => [Admin::class]
], function () {
    Route::get("/admin/messages", function () {
        return view("admin/messages");
    });

    Route::get("/admin/users/add", function () {
        return view("admin/users/add");
    });

    Route::get("/admin/users/edit/{id}", function () {
        return view("admin/users/edit", [
            "id" => request()->id ?? 0
        ]);
    });

    Route::get("/admin/users", function () {
        return view("admin/users/index");
    });

    Route::get("/admin/settings", function () {
        return view("admin/settings");
    });

    Route::get("/admin", [AdminController::class, "index"]);
});

Route::get("/admin/login", function () {
    return view("admin/login");
});
