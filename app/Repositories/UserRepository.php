<?php

namespace App\Repositories;

use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements RepositoryInterface{


    private $user;


    public function __construct(User $user){
        $this->user= $user;
    }
    public function create(array $data): array{
        $user=  $this->user->create($data);
        return ["success"=>true, "data"=>$user];
    }

    public  function find(int $id): array {
            $user=  $this->user->findOrFail($id);
            return ["success"=>true, "data"=>$user];
    }

    // public  function findUserInformation(int $id){

    //     $user = $this->user->select('id', 'first_name', 'last_name', 'phone', 'email')
    //         ->with([
    //             'roles:id,name', 
    //             'roles.permissions:id,name'
    //         ])
    //         ->findOrFail($id);

    //     $userArray = $user->toArray();  

    //     foreach ($userArray['roles'] as &$role) {
    //         $role['permissions'] = array_column($role['permissions'], 'name');
    //     }

    //     return $userArray;
    // }
    public function findUserInformation(int $id): array
{
    $user = $this->user->select('id', 'first_name', 'last_name', 'phone', 'email')
        ->with([
            'roles:id,name', 
            'roles.permissions:id,name'
        ])
        ->findOrFail($id);

    $userArray = $user->toArray();

    foreach ($userArray['roles'] as &$role) {
        $role['permissions'] = array_column($role['permissions'], 'name');
    }

    // Ensure the data structure is consistent and return as an array
    return [
        'id' => $userArray['id'],
        'first_name' => $userArray['first_name'],
        'last_name' => $userArray['last_name'],
        'phone' => $userArray['phone'],
        'email' => $userArray['email'],
        'roles' => $userArray['roles']
    ];
}



    public function all(): Collection{
        return $this->user->select("id","first_name","last_name","phone","email")->get();
    }

    public function getPage($perPage = 10, $page = 1){
        return $this->user->paginate($perPage, ["id","first_name","last_name","phone","email"], 'page', $page);
    }
   
    public function update(int $id, array $data): bool
    {
        if(!$this->user->where('id', $id)->update($data)){
            throw new Exception("failed to update user");
        }
        return true;
    }

    public function delete(int $id):bool{
        if(!$this->user->where("id", $id)->delete())
            throw new Exception("failed to delete user");
        return true;
    }

    public function count(): int{
        return $this->user->count();
    }

    public static function assignRoleToUser(User $user, string $roleName): bool
    {
        if (!$user->assignRole($roleName)) {
            throw new Exception("failed to assign role to user");
        }
        return true;
    }

    public function userHasRole(User $user, string $roleName): bool{
        return $user->hasRole($roleName);
    }

    public function exists(array $params): bool
    {
        $query = $this->user->query();
        foreach ($params as $field => $value) {
            $query->where($field, $value);
        }
        return $query->exists();
    }

    
   
}