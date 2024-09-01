<?php

namespace App\Repositories;

use Exception;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Interfaces\GenreRepositoryInterface;


class GenreRepository implements RepositoryInterface{

    private $genre;

    public function __construct(Genre $genre){
        $this->genre= $genre;
    }
   
    public function create(array $data) : array {

            $genre= $this->genre->create($data);
            return ["success"=>true, "data"=>$genre];
    }

    public function find(int $id): array {
            $genre=  $this->genre->findOrFail($id);
            return ["success"=>true, "data"=>$genre];
    }

    public function all():Collection{
        return $this->genre->select("id", "name", "description")->get();
    }

    public function update(int $id, array $data): bool {
        if (!$this->genre->where('id', $id)->update($data)) {
            throw new Exception("Failed to update genre");
        }
        return true;
    }

    public function delete(int $id): bool {
        if (!$this->genre->where('id', $id)->delete()) {
            throw new Exception("Failed to delete genre");
        }
        return true;
    }

    public function count(): int {
        return $this->genre->count();
    }

    public function exists($params): bool {
        $query = Genre::query();
        foreach ($params as $field => $value) {
            $query->where($field, $value);
        }
        return $query->exists();
    }
}

