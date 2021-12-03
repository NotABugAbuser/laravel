<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["namespace" => "Auth", "middleware" => "auth"], function(){
    Route::post("/logout", "LoginController@logout");
});
// группа авторизации
Route::group(["namespace" => "Auth"], function () {
    Route::post("/signup", "RegisterController@signup");
    Route::post("/login", "LoginController@login");
    Route::get("/login", function() {
        return response()->json(["message" => "Вы должны авторизоваться"] ,403);
    })->name("login");
});
