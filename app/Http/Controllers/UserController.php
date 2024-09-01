<?php

namespace App\Http\Controllers;

use Exception;
// use App\Models\Role;
use Validator;
// use Spatie\Permission\Models\User;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    public function index(){
        try{
            $UserService = new UserService();
            $users=$UserService->getAllUsers();
            $status= $users["success"] ? 200 : 401;
            return response()->json($users["data"], $status);

        }catch(Exception $e){
            Log::error("failed to get users: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    public function show($id){
        try{
            $user = User::find($id);
            if (!$user) 
                return response()->json(['message'  => "user not found"], 422);
            return response()->json($user, 200);
        }catch(Exception $e){
            Log::error("failed to get user: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function store(Request $request){

        try{
            $UserService= new UserService();
            $user= $UserService->createUser($request->all());
            $status= $user["success"] ? 200 : 401;
            return response()->json($user, $status);
        }catch(Exception $e){
            Log::error("Failed to register: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function destroy($userId){
        try{
            $UserService= new UserService();
            $user= $UserService->deleteUser($userId);
            $status= $user["success"] ? 200 : 401;
            return response()->json($user, $status);

        }catch(Exception $e){
            Log::error("failed to delete user: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }   
    }

    public function update($userId, Request $request){

        try{
            $UserService= new UserService();
            $user= $UserService->updateUser($userId, $request->all());
            $status= $user["success"] ? 200 : 401;
            return response()->json($user, $status);
            
        }catch(Exception $e){
            Log::error("failed to update user: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    

    public function assignRole(Request $request, $userId){

        try{
            $validator = Validator::make($request->all(), [
                'role' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
            $user = User::find($userId);
            if ($user == null) 
                return response()->json(['message'  => "user not found"], 422);

            $role = Role::find($request->input("role"));
            if($role == null) 
                return response()->json(['message'  => "role not found"], 422);
            
    
            $user->assignRole($role['name']);
            return response()->json(['message' => 'Role assigned successfully'], 200);
        }catch(Exception $e){
            Log::error("failed to assign role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function revokeRole(Request $request, $userId){

        try{
            $validator = Validator::make($request->all(), [
                'role' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
           
            $user = User::find($userId);
            if (!$user) 
                return response()->json(['message' => 'User not found'], 404);

            $role = Role::find($request->input("role"));
            if($role == null) 
                return response()->json(['message'  => "role not found"], 422);
    

            $user->removeRole($role['name']);
            return response()->json(['message' => 'Role revoked successfully'], 200);

        }catch(Exception $e){
            Log::error("failed to revoke role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function assignPermission(Request $request, $userId){

        try{
            $validator = Validator::make($request->all(), [
                'permission' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
            $user = User::find($userId);
            if ($user == null) 
                return response()->json(['message'  => "user not found"], 422);

            $permission = Permission::find($request->input("permission"));

            if($permission == null) 
                return response()->json(['message'  => "Permission not found"], 422);
            
    
            $user->assignPermissionTo($permission['name']);
            return response()->json(['message' => 'Permission assigned successfully'], 200);
        }catch(Exception $e){
            Log::error("failed to assign permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function revokePermission(Request $request, $userId){

        try{
            $validator = Validator::make($request->all(), [
                'permission' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
           
            $user = User::find($userId);
            if (!$user) 
                return response()->json(['message' => 'User not found'], 404);

            $permission = Permission::find($request->input("permission"));
            if($permission == null) 
                return response()->json(['message'  => "permission not found"], 422);
    

            $user->removeRole($permission['name']);
            return response()->json(['message' => 'Permission revoked successfully'], 200);

        }catch(Exception $e){
            Log::error("failed to revoke permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }
}
