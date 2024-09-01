<?php

namespace App\Services;

use Exception;
use App\Utils\ValidationRules;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Services\Interfaces\ServiceInterface;
use App\Services\Interfaces\HasPermissionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RoleService implements ServiceInterface, HasPermissionInterface{


    private $roleRepository;
    private $permissionRepository;


    public function __construct(RoleRepository $roleRepositry,PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepositry;
    
    }

    
    public function getAll(bool $namesOnly= null):array{


        if($namesOnly){
            $roles=  $this->roleRepository->allNames() ;
            $roles= $roles->map(function($role){
                return $role->name;
            });
        }else{
            $roles= $this->roleRepository->all();
        }
        
            if ($roles->isEmpty()) 
                return ["success"=> false,  "message"=>"No roles found"];
            return ["success"=> true,  "data"=>$roles];
        
    }

    public function find($roleId):array{
        $role= $this->roleRepository->findRoleInformation($roleId);
        return $role;
    }

    public function create(array $data):array{
        $validator = ValidationRules::validate($data, ValidationRules::$roleStoreRules); 
        if($this->roleRepository->exists(["name"=>$data["name"]]))
            return ["success"=> false, "message"=>"role already exists"];
            
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

            
        $data["guard_name"]= "api";
        $role= $this->roleRepository->create($data);

        if (isset($permissions)) {
            $syncPermissionsResult = $this->assignManyPermissions($role["data"], $permissions);
            if (!$syncPermissionsResult['success']) {
                return $syncPermissionsResult;
            }
        }
        return ["success"=>true, "role"=>$role];
    }

    public function delete(int $id):array{
        $role = $this->roleRepository->Exists(["id"=>$id]);
        if(!$role)
            throw new ModelNotFoundException("Role not found");
        $this->roleRepository->delete($id);
        return ["success"=> true,  "message"=>"role deleted successfully"];
    }

    public function update(int $id, array $data): array{
        

    $validator = ValidationRules::validate($data, ValidationRules::$roleUpdateRules);
    $role = $this->roleRepository->find($id)["data"];

    if (isset($data['permissions'])) {
        $permissions = $data['permissions'];
        unset($data['permissions']);
    }

    if (isset($permissions)) {
        $syncPermissionsResult = $this->assignManyPermissions($role, $permissions);
        if (!$syncPermissionsResult['success']) {
            return $syncPermissionsResult;
        }
    }

    $role = $this->roleRepository->update($id, $data);


    return ["success" => true, "message" => "Role updated successfully", "role" => $role];
    }

    public function assignPermission(int $id, $data):array{
        $validator = ValidationRules::validate(["permission"=>$data], ValidationRules::$permissionIdRule);
        
        $currentRole = $this->roleRepository->find($id)["data"];
        $permission= $this->permissionRepository->find($data)["data"];

        try{
            $currentRole->givePermissionTo($permission["name"]);
                // return["success" => false, 'message' => 'this role already have this permission'];
            return["success" => true, 'message' => 'Permission assigned successfully'];
        }catch(Exception $e){
            throw new \RuntimeException("Failed to assign permission to role",0, $e);
        }


    }

    public function revokePermission(int $id, $data): array
    {
        $validator = ValidationRules::validate(["permission" => $data], ValidationRules::$permissionIdRule);
        $currentRole = $this->roleRepository->find($id)["data"];
        $permission = $this->permissionRepository->find($data)["data"];
        
        if (!$currentRole->hasPermissionTo($permission["name"])) {
            return ["success" => false, "message" => "Role does not have this permission"];
        }
        $currentRole->revokePermissionTo($permission["name"]);
        return ["success" => true, "message" => "Permission revoked successfully"];
    }

    public function assignManyPermissions($role, array $permissions): array
{
    // $role = $this->roleRepository->find($roleId)["data"];
    // $invalidPermissions = $this->permissionRepository->getInvalidPermissions($permissions);

    // if (!empty($invalidPermissions)) 
    //     return [
    //         'success' => false,
    //         'message' => 'One or more permissions are invalid.',
    //         'invalid_permissions' => $invalidPermissions
    //     ];

    $role->syncPermissions($permissions);

    return [
        'success' => true,
        'message' => 'Permissions assigned successfully.',
        'permissions' => $role->getPermissionNames()
    ];
}



}