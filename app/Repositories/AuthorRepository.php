<?php

namespace App\Repositories;

use Exception;
use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AuthorRepository implements RepositoryInterface{

    private $author;

    public function __construct(Author $author){
        $this->author= $author;
    }
   
    public function create(array $data): array {
  
            $author= $this->author->create($data);
            return ["success"=>true, "data"=>$author];
    }

    public function find(int $id): array {
            $author=  $this->author->findOrFail($id);
            return ["success"=>true, "data"=>$author];
    }

    public function all():Collection{
        return $this->author->all();
    }

    public function update(int $id, array $data): bool {
        if (!$this->author->where('id', $id)->update($data)) {
            throw new Exception("Failed to update author");
        }
        return true;

    }

    public function delete($id): bool {
        // if ($this->author->where('id', $id)->delete())
        //     throw new Exception("Failed to delete author");
        return $this->author->where('id', $id)->delete();;
    }

    public function count(): int {
        return $this->author->count();
    }

    public function exists($params): bool {
        $first_name = $params['first_name'];
        $last_name = $params['last_name'];
        return $this->author->where('first_name', $first_name)->where('last_name', $last_name)->exists();
    }
}

