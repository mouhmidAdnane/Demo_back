<?php

namespace App\Services;

use Exception;
use App\Utils\ValidationRules;
use Illuminate\Support\Facades\Log;
use App\Repositories\BookRepository;
use App\Services\Interfaces\ServiceInterface;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\BookServiceInterface;
use App\Repositories\Interfaces\BookRepositoryInterface;

class BookService implements ServiceInterface{

    private $bookRepository;

    public function __construct(BookRepository $bookRepository) {
        $this->bookRepository = $bookRepository;
    }

    public function getAll(): array {
        $books = $this->bookRepository->all();
        if ($books->isEmpty()) {
            return ["success" => false, "message" => "No Books found"];
        }
        return ["success" => true, "data" => $books];

    }

    public function find(int $bookId): array {
        $book = $this->bookRepository->find($bookId);
        // if (!$book) {
        //     return ["success" => false, "message" => "Book not found"];
        // }
        return ["success" => true, "data" => $book];
    }

    public function create(array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::$bookStoreRule);

        $data["genre_id"]= isset($data["genre_id"]) ? $data["genre_id"] : null;
        $data["cover_image"]= isset($data["cover_image"]) ? $data["cover_image"] : null;
        $data["summury"]= isset($data["summury"]) ? $data["summury"] : null;
        $data["discount"]= isset($data["discount"]) ? $data["discount"] : null;

        try {
            $book = $this->bookRepository->create($data);
            return ["success" => true, "Book" => $book];
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to create Book", 0, $e);
        }
    }

    public function delete(int $id): array {
        $book = $this->bookRepository->findBook($id);
        // if (!$book) {
        //     return ["success" => false, "message" => "Book not found"];
        // }

        try {
            if($this->bookRepository->delete($id))  
                return ["success" => true, "message" => "Book deleted successfully"];
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to delete Book", 0, $e);
        }
    }

    public function update(int $id, array $data): array {

        $validator = ValidationRules::validate($data, ValidationRules::$bookUpdateRule);

        $book = $this->bookRepository->findBook($id);
        if (!$book) {
            return ["success" => false, "message" => "Book not found"];
        }

        try {
            // dd($data);
            $result= $this->bookRepository->update($id, $data);
            if($result)
                return ["success" => true, "message" => "Book updated successfully"];
        } catch (Exception $e) {
            Log::error("Failed to update book: {$e->getMessage()}", ['exception' => $e]);
            throw new \RuntimeException("Failed to update Book", 0, $e);
        }
    }
}
