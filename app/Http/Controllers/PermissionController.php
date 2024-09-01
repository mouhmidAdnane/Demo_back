<?php

namespace App\Http\Controllers;


use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function index(){
        
        try{
            $permissions = Permission::select("id", "name", "description")->get();
            if (!$permissions) 
                return response()->json([
                        'message'  => "no permissions found",
                    ], 422);
            return response()->json($permissions, 200);
        }catch(Exception $e){
            Log::error("failed to get permissions: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function show($permissionsId){
        try{
            $permission = Permission::select("id", "name", "description")->find($permissionsId);
            if (!$permission) 
                return response()->json([
                        'message'  => "permission not found",
                    ], 422);
            return response()->json($permission, 200);
        }catch(Exception $e){
            Log::error("failed to get permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    public function store(Request $request){   
        try{
            $validator = Validator::make($request->all(), [
                "name" => "required|max:125|unique:permissions",
                'description' => 'max:511'
            ]);
            
            if ($validator->fails()) 
                return response()->json(["message" => $validator->errors()], 422);

            $description= $request->input("description") ? $request->input("description") : null;
            $permission= Permission::create(["name"=>$request->input("name"), "description"=> $description]);

            return response()->json(["message"=>"premission created successfully", "permission"=>$permission], 200);

        }catch(Exception $e){
            Log::error("failed to create permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function destroy($permissionsId){

        try {
            $permission = Permission::find($permissionsId)->first();
            if ($permission == null) 
                return response()->json(['message'  => "permission not found"], 422);

            $permission->delete();
            return response()->json([
                    'message' => 'Permission deleted successfully',
                ], 200);
        } catch (Exception $e) {
            Log::error("failed to delete permission: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
     
    }

    public function update($permissionsId, Request $request){

        try{
        $validator = Validator::make($request->all(), [
            'name' => 'max:125|unique:permissions',
            'description' => 'max:511'
        ]);
        
        if ($validator->fails()) 
            return response()->json(["message" => $validator->errors()], 422);
        
        $permission = Permission::find($permissionsId)->first();
        
        if (!$permission) 
            return response()->json(["message" => "Permission not found"], 404);
        
        $updateData = ["name" => $request->input('name'), "description" => $request->input('description')];
        $permission->update($updateData);
        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission
        ], 200);
    }catch(Exception $e){
        Log::error("failed to update permission: {$e->getMessage()}");
        return response()->json(['message' => 'Server error', 'error'=>$e,500]);
    }
}
}
