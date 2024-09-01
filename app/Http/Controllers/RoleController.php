<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\RoleService;

// use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    public function index(){
        try{
            $RoleService= new RoleService();
            $roles= $RoleService->getAllRoles();
            $status= $roles["success"] ? 200 : 401;
            return response()->json($roles["data"], $status);
        }catch(Exception $e){
            Log::error("failed to get roles: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function show($roleId){
        try{
            $RoleService= new RoleService();
            $role= $RoleService->getRoleById($roleId);
            $status= $role["success"] ? 200 : 401;
            return response()->json($role["data"], $status);
        }catch(Exception $e){
            Log::error("failed to get role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error' , 'error'=>$e->getMessage() ,500]);
        }
    }

    public function store(Request $request){

        try{
            $RoleService= new RoleService();
            $role= $RoleService->createRole($request->all());
            $status= $role["success"] ? 200 : 401;
            return response()->json($role, $status);
        }catch(Exception $e){
            Log::error("failed to store role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error' ,'error'=>$e->getMessage() ,500]);
        }
    }

    public function destroy($roleId){
        try{
            $RoleService= new RoleService();
            $role= $RoleService->deleteRole($roleId);
            $status= $role["success"] ? 200 : 401;
            return response()->json($role, $status);
        }catch(Exception $e){
            Log::error("failed to delete role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
        
    }

    public function update($roleId, Request $request){
        try{
            $RoleService= new RoleService();
            $role= $RoleService->updateRole($roleId, $request->all());
            
            switch($role["message"]){
                case "role not found":
                    $status= 404;
                case "role updated successfully":
                    $status= 200;
                    return response()->json($role, 401);
                default:
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:125|unique:roles',
                'description' => 'max:511'
            ]);
            
            if ($validator->fails())
                return response()->json(["message" => $validator->errors()], 422);

            $role = Role::find($roleId)->first();
            
            if ($role== null) 
                return response()->json(["message" => "Role not found"], 404);
            
            $updatedData = ["name" => $request->input('name'), "description" => $request->input('description') ? $request->input('description') : null];
            $role->update($updatedData);
            
            return response()->json([
                'message' => 'Role updated successfully',
                'role' => $role
            ], 200);
        }catch(Exception $e){
            Log::error("failed to update role: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    public function assignPermission($roleId, Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'permission' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
            $currentRole = Role::find($roleId);
            if (!$currentRole) 
                return response()->json(['message'  => "role not found"], 422);

                $permission= Permission::find($request->input('permission'));
            if(!$permission)
                return response()->json(['message'  => "permission not found"], 422);
    
            
            $currentRole->givePermissionTo($permission["name"]);
            return response()->json(['message' => 'Permission assigned successfully'], 200);
        }catch(Exception $e){
            Log::error("failed to assign permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    public function revokePermission($roleId, Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'permission' => 'required|numeric'
            ]);
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);
            
            
            $currentRole = Role::find($roleId);
            if (!$currentRole) 
                return response()->json(['message'  => "role not found"], 422);
            
            $permission= Permission::find($request->input('permission'));
            if (!$permission) 
                return response()->json(['message'  => "permission not found"], 422);
            
            $currentRole->revokePermissionTo($permission["0"]->name);
            return response()->json(['message' => 'Permission revoked successfully'], 200);

        }catch(Exception $e){
            Log::error("failed to revoke permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }
}
