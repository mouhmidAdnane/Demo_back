<?php

namespace App\Services;

use Exception;
use App\Utils\ValidationRules;
use Illuminate\Support\Facades\Log;
use Dotenv\Exception\ValidationException;
use App\Repositories\PermissionRepository;
use App\Services\Interfaces\ServiceInterface;
use App\services\Interfaces\PermissionServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService implements ServiceInterface{

    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository) {
        $this->permissionRepository = $permissionRepository;
    }

    public function getAll(): array {
        $permissions = $this->permissionRepository->all();
        if ($permissions->isEmpty()) {
            return ["success" => false, "message" => "No Permissions found"];
        }
        return ["success" => true, "data" => $permissions];
    }

    public function find(int $id): array {
        $permission = $this->permissionRepository->find($id);
        return $permission;
    }

    public function create(array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::$permissionStoreRule);
        
        if ($this->permissionRepository->exists(["name" =>$data["name"]]))
            return ["success" => false, "message" => "Permission already exists"];
        
        $permission = $this->permissionRepository->create($data);
        return ["success" => true, "Permission" => $permission];
    }



    public function delete(int $id): array {
        $permission = $this->permissionRepository->exists(["id" => $id]);
        if (!$permission) 
           throw new ModelNotFoundException("Permission not found");

        $result = $this->permissionRepository->delete($id);
        return ["success" => true, "messsage" => "permission deleted successfully"]; 
    }

    public function update(int $id, array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::$permissionUpdateRule);

        $permission = $this->permissionRepository->find($id)["data"];
        $this->permissionRepository->update($permission, $data);
        return ["success" => true, "message" => "Permission updated successfully"];
    }
}
