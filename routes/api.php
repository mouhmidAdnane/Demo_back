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
    
    Route::group(['middleware' => 'role:admin'], function (){

        Route::prefix('roles')->group(function () {
            Route::resource('/', RoleController::class)->names([
                'index' => 'roles.index',
                'store' => 'roles.store',
                'update' => 'roles.update',
                'destroy' => 'roles.destroy',
            ]);
            Route::post('{role}/permissions', [RoleController::class, 'assignPermission']);
    
        });
    });

    Route::group(['middleware' => 'role:manager'], function (){
        Route::prefix('permissions')->group(function () {
            Route::resource('/', PermissionController::class)->names([
                'index' => 'permissions.index',
                'store' => 'permissions.store',
                'update' => 'permissions.update',
                'destroy' => 'permissions.destroy',
            ]);
    
        });

    });
    
    Route::prefix('users')->group(function () {
        Route::post('/{id}/roles', [UserController::class, 'assignRole']);
        Route::delete('/{id}/roles', [UserController::class, 'revokeRole']);
    });
    
    
    
    
});


