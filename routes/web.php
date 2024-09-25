<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;

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

Route::get("/admin/login", function () {
    return view("admin/login");
});

Route::get("/admin", [AdminController::class, "index"]);

Route::get("/email-verification/{email}", function () {
    return view("email-verification", [
        "email" => request()->email
    ]);
});

Route::get("/reset-password/{email}/{token}", function () {
    return view("reset-password", [
        "token" => request()->token,
        "email" => request()->email
    ]);
})
    ->name("password.reset");

Route::get("/forgot-password", function () {
    return view("forgot-password");
})->name("password.request");

Route::get("/change-password", function () {
    return view("change-password");
});

Route::get("/profile", function () {
    return view("profile");
});

Route::get("/login", function () {
    return view("login");
});

Route::get("/register", function () {
    return view("register");
});

Route::get("/", function () {
    return view("home");
});

// Route::get('/', function () {
//     return view('welcome');
// });
