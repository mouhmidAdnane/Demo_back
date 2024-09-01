<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Interfaces\PermissionRepositoryInterface;


class PermissionRepository implements RepositoryInterface{

    private $permission;

    public function __construct(Permission $permission){
        $this->permission= $permission;
    }
   
    public function create(array $data): array {
        
        $permission=  $this->permission->create($data);
        return ["success"=>true, "data"=>$permission];
       
    }


    public function find(int $id): array {

            $permission=  $this->permission->findOrFail($id);
            return ["success"=>true, "data"=>$permission];
    }

    
    public function all():Collection{
        return $this->permission->select("id", "name", "description")->get();
    }

    public function update(int $id, array $data): bool {
        if (!$this->permission->where('id', $id)->update($data))
            throw new Exception("Failed to update permission");
        return true;
    }

    public function delete(int $id): bool {
        if (!$this->permission->where('id', $id)->delete()) 
            throw new Exception("Failed to delete permission");
        return true;
    }

    public function count(): int {
        return $this->permission->count();
    }

    public function exists(array $params): bool {

        $query = $this->permission->query();
        foreach ($params as $field => $value) {
            $query->where($field, $value);
        }
        return $query->exists();
       
    }

    public function getInvalidPermissions(array $permissions): array{
    $validPermissions = $this->permission->whereIn('name', $permissions)->pluck('name')->toArray();
    return array_diff($permissions, $validPermissions);
}

}

