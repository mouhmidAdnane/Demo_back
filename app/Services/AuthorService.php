<?php

namespace App\Services;

use Exception;
use App\Utils\ValidationRules;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Services\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\AuthorRepository;

class AuthorService implements ServiceInterface{

    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository) {
        $this->authorRepository = $authorRepository;
    }

    public function getAll(): array {
        $authors = $this->authorRepository->all();
        if ($authors->isEmpty()) {
            return ["success" => false, "message" => "No Authors found"];
        }
        return ["success" => true, "data" => $authors];
    }

    public function find(int $id): array {
        $author = $this->authorRepository->find($id);
        // if (!$author) {
        //     return ["success" => false, "message" => "Author not found"];
        // }
        return ["success" => true, "data" => $author];
    }

    public function create(array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::authorStoreRule());
        $params= ['first_name'=>$data["first_name"], 'last_name'=>$data["last_name"]];

        if ($this->authorRepository->exists($params))
            return ["success" => false, "message" => "Author already exists"];
        
        try {
            $author = $this->authorRepository->create($data);
            return ["success" => true, "Author" => $author];
        } catch (Exception $e) {
            Log::error("Failed to create author: {$e->getMessage()}", ['exception' => $e]);
            throw new \RuntimeException("Failed to create Author", 0, $e);
        }
    }

    public function delete(int $id): array {
        $author = $this->authorRepository->find($id);
        if (!$author)  
            return new ModelNotFoundException("Author not found");

            $result = $this->authorRepository->delete($id);
            if ($result) {
                return ["success" => true, "message" => "Author deleted successfully"];
            }

    }

    public function update(int $authorId, array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::authorUpdateRule());

        $author = $this->authorRepository->find($authorId);
        if (!$author) {
            return new ModelNotFoundException("Author not found");
        }

        try {
            // dd($data);
            $result= $this->authorRepository->update($authorId, $data);
            if($result)
                return ["success" => true, "message" => "Author updated successfully"];
        } catch (Exception $e) {
            Log::error("Failed to update author: {$e->getMessage()}", ['exception' => $e]);
            throw new \RuntimeException("Failed to update Author", 0, $e);
        }
    }
}
