<?php

namespace App\Repositories;

use Exception;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleRepository implements RepositoryInterface{

    private $role;

    public function __construct(Role $role){
        $this->role= $role;
    }

    public  function create(array $data):array {
        $role= $this->role->create($data);
        return ["success"=>true, "data"=>$role];
    }
        
    public function find(int $id): array{
            $role = $this->role->findOrFail($id);

            return ["success" => true, "data" => $role];
        
    }

    public function findRoleInformation(int $id): array{
            $role = $this->role->with('permissions')->findOrFail($id);

            // Convert the role model to an array
            $roleArray = $role->toArray();
    
            // Transform permissions to include only the names
            foreach ($roleArray['permissions'] as &$permission) {
                $permission = $permission['name'];  // Access the name directly
            }
    
            return ["success" => true, "data" => $roleArray];
        
    }


    
    public function all(): Collection { 
        return $this->role->select("id","name","description")->get();
    }


    public function allNames():collection{
        return $this->role->select("name")->get();
    }

    public function getInvalidRoles($roles):array{
        $validRoles = $this->role->whereIn('name', $roles)->pluck('name')->toArray();
        return array_diff($roles, $validRoles);
    }


    public function update(int $id, array $data): bool
    {
        if(!$this->role->where('id', $id)->update($data)){
            throw new Exception("failed to update role");
        }
        return true;
    }

    public function delete(int $id):bool
    {
        if(!$this->role->where('id', $id)->delete())
            throw new Exception("failed to delete role");
        return true;
    }

    public  function count(): int
    {
        return Role::count();
    }

    public function exists(array $params): bool
    {
        $query = $this->role->query();
        foreach ($params as $field => $value) {
            $query->where($field, $value);
        }
        return $query->exists();
    }
    

}