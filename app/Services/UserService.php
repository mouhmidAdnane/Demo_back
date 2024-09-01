<?php

namespace App\Services;

use App\Utils\userHelper;
use App\Utils\ValidationRules;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\PermissionRepository;
use App\Services\Interfaces\HasRoleInterface;
use App\Services\Interfaces\ServiceInterface;
use App\Services\Interfaces\AuthentifiableInterface;
use App\Services\Interfaces\UserHasPermissionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;

class UserService implements ServiceInterface, AuthentifiableInterface, HasRoleInterface , UserHasPermissionInterface
{

    private $userRepository;
    private $roleRepository;
    // private $permissionRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }



    public function create(array $data): array
    {
        $validator = ValidationRules::validate($data, ValidationRules::$userStoreRules);
        $data["password"] = UserHelper::hashPassword($data["password"]);
        unset($data['c_password']);
        $user = $this->userRepository->create($data)["data"];

        if (isset($data['roles'])) {
            $assignRolesResult = $this->assignManyRoles($user, $data['roles']);
            if (!$assignRolesResult['success'])
                return $assignRolesResult;

            return [
                "success" => true,
                "message" => "User created and roles assigned successfully",
                "user" => $user,
                "roles" => $assignRolesResult['roles']
            ];
        }

        return [
            "success" => true,
            "message" => "User created successfully",
            "user" => $user
        ];
    }

    public function update(int $id, array $data): array
    {
        $validator = ValidationRules::validate($data, ValidationRules::userUpdateRules($id));
        $user = $this->userRepository->find($id)["data"];


        if (isset($data["roles"])){
            $roles= $data["roles"];
            unset($data['roles']);
        }
        
        if (isset($data["password"]))
            $data["password"] = UserHelper::hashPassword($data["password"]);
    
        unset($data['c_password']);

        if (isset($roles)) {
            
            $assignRolesResult = $this->assignManyRoles($user, $roles);
            if (!$assignRolesResult['success'])
                return $assignRolesResult;
        }

        $user = $this->userRepository->update($id, $data);
        return ["success" => true,  "message" => "user updated successfully", "user" => $user];
    }

    public function delete(int $id): array
    {
        $user = $this->userRepository->exists(["id" => $id]);
        if (!$user)
            throw new ModelNotFoundException("User not found");
        $result = $this->userRepository->delete($id);
        if ($result)
            return ["success" => true,  "message" => "user deleted successfully"];
    }

    public function find($id): array
    {
        $user = $this->userRepository->find($id);
        return ["success" => true,  "user" => $user];
    }

    public function getUserInformation(int $id): array
    {
        $result =  $this->userRepository->findUserInformation($id);

        $user = [
            'id' => $result['id'],
            'first_name' => $result['first_name'],
            'last_name' => $result['last_name'],
            'email' => $result['email'],
            'phone' => $result['phone']
        ];
        $roles = $result['roles'];
        // $permissions = $result->permissions;

        return [
            'success' => true,
            'user' => $user,
            'roles' => $roles,
            // 'permissions' => $permissions
        ];
    }

    public function getAll(int $perPage = 10, ?int $page = null): array
    {
        $result= [];
        if ($page === null) {
            $users = $this->userRepository->all();
    
            if ($users->isEmpty()) 
                return [
                    'success' => false,
                    'message' => 'No users found',
                ];

            $result['success'] = true;
            $result['data'] = $users;
    
        } else {
            $users = $this->userRepository->getPage($perPage, $page);
    
            if ($users->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No users found',
                ];
            }

            $metadata = [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ];

            $result['success'] = true;
            $result['data'] = $users->items();
            $result['metadata'] = $metadata;
        }

        return $result;
    }



    public function regiser(array $data): array
    {
        $validator = ValidationRules::validate($data, ValidationRules::$userStoreRules);
        $user = $this->userRepository->create($data);
        $success["token"] = $user["data"]->createToken("demo")->accessToken;
        return ['success' => true, 'user' => $user["data"], 'token' => $success["token"]];
    }

    // public function login(array $data): array
    // {

    //     $validator = ValidationRules::validate($data, ValidationRules::$userLoginRules);
    //     $loginCheck = UserHelper::attemptLoging($data);
    //     if (!$loginCheck)
    //         return ["success" => false, "message" => "Invalid credentials"];

    //     $userId = Auth::id();
    //     $user = $this->userRepository->findUserInformation($userId);

    //     if (!$user) {
    //         return [
    //             'success' => false,
    //             'message' => 'Authentication failed'
    //         ];
    //     }

    //     // dd($user);
    //     return [
    //         'success' => true,
    //         'user' =>  [
    //             'id' => $user["id"],
    //             'first_name' => $user["first_name"],
    //             'last_name' => $user["last_name"],
    //             'email' => $user["email"],
    //             'phone' => $user["phone"]
    //         ],
    //         'roles' => $user["roles"],
    //         // 'permissions'=> $user["permissions"],
    //         'token' => $user->createToken('demo')->accessToken
    //     ];
    // }

    public function login(array $data): array
{
    $validator = ValidationRules::validate($data, ValidationRules::$userLoginRules);
    $loginCheck = UserHelper::attemptLoging($data);
    if (!$loginCheck) {
        return ["success" => false, "message" => "Invalid credentials"];
    }

    $userId = Auth::id();
    $user = $this->userRepository->findUserInformation($userId);

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Authentication failed'
        ];
    }

    return [
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ],
        'roles' => $user['roles'],
        'token' => Auth::user()->createToken('demo')->accessToken
    ];
}







    public function assignManyRoles(User $user, array $roles): array
    {

        // $user = $this->userRepository->find($id)["data"];
        // $invalidRoles = $this->roleRepository->getInvalidRoles($roles);

        // if (!empty($invalidRoles)) {
        //     return [
        //         'success' => false,
        //         'message' => 'One or more roles are invalid.',
        //         'invalid_roles' => $invalidRoles
        //     ];
        // }
        $user->syncRoles($roles);
        return [
            'success' => true,
            'message' => 'Roles assigned successfully.',
            'roles' => $user->getRoleNames()
        ];
    }

    public function assignRole(int $id, int $roleId): array
    {

        $validator = ValidationRules::validate(["role" => $roleId], ValidationRules::$roleIdRule);
        $user = $this->userRepository->find($id)["data"];
        $role = $this->roleRepository->find($roleId)["data"];
        if($user->hasRole($role->name))
            return ["success" => false,  "message" => "user already has this role"];
        $result = $this->userRepository->assignRoleToUser($user, $role->name);
        if ($result)
            return ["success" => true,  "message" => "role assigned successfully"];
    }

    public function revokeRole(int $id, $data): array
    {
        $validator = ValidationRules::validate(["role" => $data], ValidationRules::$roleIdRule);
        $user = $this->userRepository->find($id)["data"];
        $role = $this->roleRepository->find($data)["data"];

        if (!$this->userRepository->userHasRole($user, $role->name))
            return ["success" => false, "message" => "User does not have this role"];

        $user->removeRole($role->name);
        return ["success" => true, "message" => "Role revoked successfully"];
    }


    public function assignManyPermissions(  PermissionRepository $permissionRepository, int $id, array $permissions): array{

    $user = $this->userRepository->find($id)["data"];
    $invalidPermissions = $permissionRepository->getInvalidPermissions($permissions);

    if (!empty($invalidPermissions)) {
        return [
            'success' => false,
            'message' => 'One or more permissions are invalid.',
            'invalid_permissions' => $invalidPermissions
        ];
    }

    $user->syncPermissions($permissions);

    return [
        'success' => true,
        'message' => 'Permissions assigned successfully.',
        'permissions' => $user->getPermissionNames()
    ];
}

public function assignPermission(PermissionRepository $permissionRepository, int $id, int $permissionId): array
{
    $validator = ValidationRules::validate(["permission" => $permissionId], ValidationRules::$permissionIdRule);
    $user = $this->userRepository->find($id)["data"];
    $permission = $permissionRepository->find($permissionId)["data"];

    if ($user->hasPermissionTo($permission->name)) {
        return [
            'success' => false,
            'message' => 'User already has this permission.'
        ];
    }
    $user->givePermissionTo($permission->name);
    return ["success" => true, "message" => "Permission assigned successfully"];
}

public function revokePermission(PermissionRepository $permissionRepository, int $id, $data): array
{
    $validator = ValidationRules::validate(["permission" => $data], ValidationRules::$permissionIdRule);
    $user = $this->userRepository->find($id)["data"];
    $permission = $permissionRepository->find($data)["data"];

    if (!$user->hasPermission($user, $permission->name)) {
        return ["success" => false, "message" => "User does not have this permission"];
    }

    $user->revokePermissionTo($permission->name);
    return ["success" => true, "message" => "Permission revoked successfully"];
}

}
