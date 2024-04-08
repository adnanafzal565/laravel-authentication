<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/reset-password/{email}/{token}", function () {
    return view("reset-password", [
        "token" => request()->token,
        "email" => request()->email
    ]);
})
    ->name("password.reset");

Route::get("/forgot-password", function () {
    return view("forgot-password");
});

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
