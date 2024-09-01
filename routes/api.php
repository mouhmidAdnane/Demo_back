<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function(){
    
    // Route::group(['middleware' => 'role:admin'], function (){

        
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::get('{roleId}', [RoleController::class, 'show']);
            Route::post('/', [RoleController::class, 'store']);
            Route::patch('{roleId}', [RoleController::class, 'update']);
            Route::delete('{roleId}', [RoleController::class, 'destroy']);
            Route::post('{roleId}/permissions', [RoleController::class, 'assignPermission']);
            Route::delete('{roleId}/permissions', [RoleController::class, 'revokePermission']);
        });
        
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('{userId}', [UserController::class, 'show']);
            Route::patch('{userId}', [UserController::class, 'update']);
            Route::delete('{userId}', [UserController::class, 'destroy']);
            Route::post('{userId}/roles', [UserController::class, 'assignRole']);
            Route::delete('{userId}/roles', [UserController::class, 'revokeRole']);
            Route::post('/{userId}/permissions', [UserController::class, 'assignPermission']);
            Route::delete('{userId}/permissions', [UserController::class, 'revokePermission']);
        });
        // Route::group(['middleware' => 'role:manager'], function (){
            Route::prefix('permissions')->group(function () {
                Route::get('/', [PermissionController::class, 'index']);
                Route::get('{permissionId}', [PermissionController::class, 'show']);
                Route::patch('{permissionId}', [PermissionController::class, 'update']);
                Route::delete('{permissionId}', [PermissionController::class, 'destroy']);
                Route::post('/', [PermissionController::class, 'store']);
                
            });
    
            // });
        });

// });


