<?php 

namespace App\Services\Interfaces;

use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;

Interface HasPermissionInterface{

    public function assignPermission(int $id, int $permissionId): array;
    public function revokePermission( int $id, array $data): array;
    public function assignManyPermissions( int $roleId, array $permissions): array;
}

Interface UserHasPermissionInterface{

    public function assignPermission(PermissionRepository $permissionRepository, int $id, int $permissionId): array;
    public function revokePermission(PermissionRepository $permissionRepository, int $id, array $data): array;
    public function assignManyPermissions(PermissionRepository $permissionRepository, int $roleId, array $permissions): array;
}