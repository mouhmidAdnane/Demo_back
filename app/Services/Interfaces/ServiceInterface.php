<?php
namespace App\Services\Interfaces;

use App\Repositories\RoleRepository;


interface ServiceInterface
{
    
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): array;
    public function getAll():array;
    public function find(int $id): array;
    // public function countAuthors(): ;
}