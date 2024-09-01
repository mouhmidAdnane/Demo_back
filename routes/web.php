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

Route::get('/', function () {
    return view('welcome');
});


// Route::get("/hello", function (){
//     return "Hello world!";
// })->name("hello");

// Route::get("/users/{name?}", function ($name){
//     return $name;
// });
// Route::get("/users/{id}", function ($id){
//     return "User ". $id;
// });

Route::prefix("admin")->group(function(){

    Route::get("/", function(){
        return "admin dashboard";
    });
    Route::get("/users", function(){
        return "users";
    });
});

