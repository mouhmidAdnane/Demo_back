<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PermissionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get("/login", function (){
    return response()->json(["error"=> "unauthorized", "test"=>"test"], 401);
})->name("login");




Route::group(['middleware' => 'auth:api'], function(){
    
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('{roleId}', [RoleController::class, 'show'])->where(['roleId' => '[0-9]+']);
        Route::post('/', [RoleController::class, 'store']);
        Route::patch('{roleId}', [RoleController::class, 'update'])->where(['roleId' => '[0-9]+']);
        Route::delete('{roleId}', [RoleController::class, 'destroy'])->where(['roleId' => '[0-9]+']);
        Route::post('{roleId}/permissions', [RoleController::class, 'assignPermission'])->where(['roleId' => '[0-9]+']);
        Route::delete('{roleId}/permissions', [RoleController::class, 'revokePermission'])->where(['roleId' => '[0-9]+']);
    });
    // Route::group(['middleware' => 'role:manager'], function (){

    // Route::middleware(['role:manager'])->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('{userId}', [UserController::class, 'show'])->where(['userId' => '[0-9]+']);
        Route::patch('{userId}', [UserController::class, 'update'])->where(['userId' => '[0-9]+']);
        Route::delete('{userId}', [UserController::class, 'destroy'])->where(['userId' => '[0-9]+']);
        Route::post('{userId}/roles', [UserController::class, 'assignRole'])->where(['userId' => '[0-9]+']);
        Route::delete('{userId}/roles', [UserController::class, 'revokeRole'])->where(['userId' => '[0-9]+']);
        Route::post('/{userId}/permissions', [UserController::class, 'assignPermission'])->where(['userId' => '[0-9]+']);
        Route::delete('{userId}/permissions', [UserController::class, 'revokePermission'])->where(['userId' => '[0-9]+']);
    });
// }); 
// Route::middleware(['permission:manageUsers'])->group(function () {
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::get('{permissionId}', [PermissionController::class, 'show'])->where(['permissionId' => '[0-9]+']);
            Route::patch('{permissionId}', [PermissionController::class, 'update'])->where(['permissionId' => '[0-9]+']);
            Route::delete('{permissionId}', [PermissionController::class, 'destroy'])->where(['permissionId' => '[0-9]+']);
            Route::post('/', [PermissionController::class, 'store']);
        });
    // });

    Route::prefix('genres')->group(function () {
        Route::get('/', [GenreController::class, 'index']);            
        Route::post('/', [GenreController::class, 'store']);
        Route::get('{genreId}', [GenreController::class, 'show'])->where(['genreId' => '[0-9]+']);
        Route::patch('{genreId}', [GenreController::class, 'update'])->where(['genreId' => '[0-9]+']);
        Route::delete('{genreId}', [GenreController::class, 'destroy'])->where(['genreId' => '[0-9]+']);
    });

    Route::prefix('authors')->group(function () {
        Route::get('/', [AuthorController::class, 'index']);             
        Route::post('/', [AuthorController::class, 'store']);            
        Route::get('{authorId}', [AuthorController::class, 'show'])->where(['authorId' => '[0-9]+']);  
        Route::patch('{authorId}', [AuthorController::class, 'update'])->where(['authorId' => '[0-9]+']);  
        Route::delete('{authorId}', [AuthorController::class, 'destroy'])->where(['authorId' => '[0-9]+']);  
    });
    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index']);             
        Route::get('{bookId}', [BookController::class, 'show'])->where(['bookId' => '[0-9]+']);
        Route::post('/', [BookController::class, 'store']);            
        Route::patch('{bookId}', [BookController::class, 'update'])->where(['bookId' => '[0-9]+']);
        Route::delete('{bookId}', [BookController::class, 'destroy'])->where(['bookId' => '[0-9]+']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

});
    




