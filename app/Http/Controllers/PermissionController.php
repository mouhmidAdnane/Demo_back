<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Validator;


class PermissionController extends Controller
{
    public function index(){
        $permissions = Permission::all();
            if (count($permissions) == 0) {
            return response()->json([
                    'message'  => "no permissions found",
                ], 422);
        } else{
            return response()->json($permissions, 200);
        }
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            "name" => "required|max:125|unique:permissions",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()], 422);
        }
        
        $permission= Permission::create(["name"=>$request->input("name")]);
        return response()->json(["message"=>"premission created successfully", "permission"=>$permission], 200);
    }

    public function destroy(Request $request ,$name){

        $permission = Permission::where('name', $name)->first();
        if ($permission == null) {
            return response()->json([
                'message'  => "permission not found",
            ], 422);
        } else{
            $permission->delete();
            return response()->json([
                'message' =>'Permission deleted successfully',
            ], 200);
        }
    }

    public function update($currentPermission, Request $request){

    $validator = Validator::make($request->all(), [
        'name' => 'required|max:125|unique:permissions',
    ]);
    
    if ($validator->fails()) {
        return response()->json(["message" => $validator->errors()], 422);
    }
    
    $permission = Permission::where('name', $currentPermission)->first();
    
    if ($permission== null) {
        return response()->json(["message" => "Permission not found"], 404);
    }
    
    $updatedName = ["name" => $request->input('name')];
    $permission->update($updatedName);
    
    return response()->json([
        'message' => 'Permission updated successfully',
        'permission' => $permission
    ], 200);
}
}
