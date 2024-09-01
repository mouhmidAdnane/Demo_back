<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Validator;

class RoleService{

    public static $storeRules= [
        "name" => "required|max:125|unique:roles",
        'description' => 'max:511'
    ];

    public static $updateRules= [
        'name' => 'max:125|unique:roles',
        'description' => 'max:511'  
    ];

    public static function validate(array $data, array $rules){
        $validator = Validator::make($data, $rules);
        dd($validator);
        
        if ($validator->fails())
            return ["message" => $validator->errors()];
        return null;
    }

    public function getAllRoles():array{
        $roles = Role::select("id","name","description")->get();
            if (!$roles) 
                return ["success"=> false,  "message"=>"No roles found"];
            return ["success"=> true,  "data"=>$roles];
        
    }

    public function getRoleById($roleId):array{
        $role= Role::with("permissions")->find($roleId);
            $result = [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('name')
            ];

            if (!$role) 
                return ["success"=> false,  "message"=>"role not found"];
            return ["success"=> true,  "data"=>$result];
    }

    public function createRole(array $data):array{
        $validator = Validator::make($data, Role::$storeRules);
        
        if ($validator->fails())
            return ["success"=> false,  "message"=>$validator->errors()];

        if(!Role::where("name", $data["name"])->exists());
            return ["success"=> false,  "message"=>"Role aleary exist!"];

        $description= $data["description"] ? $data["description"] : null;
        $data= ["name"=>$data["name"], "description"=> $description];  
          
        $role= Role::create($data);
        return ["success"=> true, "role"=>$role];
    }

    public function deleteRole($roleId):array{
        $role = Role::find($roleId)->first();
        if ($role == null) 
            return ["success"=> false,  "message"=>"role not found"];
    
        $role->delete();
        return ["success"=> true,  "message"=>"role deleted successfully"];
    }

    public function updateRole($roleId, array $data){
        $validator = self::validate($data, self::$updateRules);
        
        if ($validator != null)
            return $validator;
        $role = Role::find($roleId)->first();

        if ($role== null) 
            return ["success"=> false,  "message"=>"role not found"];
        
        $updatedData = ["name" => $data['name'], "description" => $data['description'] ? $data['description'] : null];
        $role->update($updatedData);
        
        return ["success"=> true,  "message"=>"role updated successfully"];
    }
}