<?php

namespace App\Repositories;

use Exception;
use App\Models\Book;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BookRepository implements RepositoryInterface{

    private $book;

    public function __construct(Book $book){
        $this->book= $book;
    }
   
  
    public function create(array $data): array {
            $book= $this->book->create($data);
            return ["success"=>true, "data"=>$book];
    }

    public function find(int $id): array {
        $book=  $this->book->with(['author', 'genre'])->findOrFail($id);
        return ["success"=>true, "data"=>$book];
    }

    public function findBook(int $id): ?Book {
        return  $this->book->findOrFail($id);
    }

    public function all():Collection{
        return $this->book->all();
    }

    public function update(int $id, array $data): bool {
        if (!$this->book->where('id', $id)->update($data)) {
            throw new Exception("Failed to update book");
        }
        return true;
    }

    public function delete(int $id): bool {
        if (!$this->book->where('id', $id)->delete()) {
            throw new Exception("Failed to delete book");
        }
        return true;
    }
    
    public function count(): int {
        return $this->book->count();
    }

    public function exists(array $params): bool {
        return $this->book->where('id', $params['id'])->exists();
    }
    /**
     * Check if a book with a given name exists.
     *
     * @param string $name
     * @return bool
     */
}

