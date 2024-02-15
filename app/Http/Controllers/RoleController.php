<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Validator;

class RoleController extends Controller
{

    public function index(){
        $roles = Role::all();
            if (count($roles) == 0) {
            return response()->json([
                    'message'  => "no roles found",
                ], 422);
        } else{
            return response()->json($roles, 200);
        }
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            "name" => "required|max:125|unique:roles",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()], 422);
        }
        
        $role= Role::create(["name"=>$request->input("name")]);
        return response()->json(["message"=>"role created successfully", "role"=>$role], 200);
    }

    public function destroy(Request $request ,$name){

        $role = Role::where('name', $name)->first();
        if ($role == null) {
            return response()->json([
                'message'  => "role not found",
            ], 422);
        } else{
            $role->delete();
            return response()->json([
                'message' =>'Role deleted successfully',
            ], 200);
        }
    }

    public function update($currentRole, Request $request){

    $validator = Validator::make($request->all(), [
        'name' => 'required|max:125|unique:roles',
    ]);
    
    if ($validator->fails()) {
        return response()->json(["message" => $validator->errors()], 422);
    }
    
    $role = Role::where('name', $currentRole)->first();
    
    if ($role== null) {
        return response()->json(["message" => "Role not found"], 404);
    }
    
    $updatedName = ["name" => $request->input('name')];
    $role->update($updatedName);
    
    return response()->json([
        'message' => 'Role updated successfully',
        'role' => $role
    ], 200);
    }

    function assignPermission($role, Request $request){

        $validator = Validator::make($request->all(), [
            'permission' => 'required|max:125|exists:permissions,name'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()], 422);
        }

        $currentRole = Role::where('name',$role)->first();
        if ($currentRole == null) {
            return response()->json([
                'message'  => "role not found",
            ], 422);
        }
        $currentRole->givePermissionTo($request->input('permission'));
        return response()->json([
        'message' => 'Permission assigned successfully',
        ], 200);
        
    }
}
