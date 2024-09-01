<?php 

namespace App\Services\Interfaces;


use App\Models\User;
// use App\Repositories\RoleRepository;

Interface HasRoleInterface{

    public function assignRole(int $id, int $roleId): array;
    public function assignManyRoles(User $user, array $roles): array;
    public function revokeRole(int $id, $data): array;

}