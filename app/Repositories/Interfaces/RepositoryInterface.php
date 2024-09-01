<?php 

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

Interface RepositoryInterface{
    public function create(array $data): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all():Collection;
    public function find(int $id): array;
    public function count(): int;
    public function exists(array $params): bool;
}