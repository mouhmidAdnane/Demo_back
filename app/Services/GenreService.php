<?php

namespace App\Services;

use Exception;
use Validator;
use App\Utils\ValidationRules;
use Illuminate\Support\Facades\Log;
use App\Repositories\GenreRepository;
use Dotenv\Exception\ValidationException;
use App\Services\Interfaces\ServiceInterface;
use App\Services\Interfaces\GenreServiceInterface;
use App\Repositories\Interfaces\GenreRepositoryInterface;

class GenreService implements ServiceInterface{

    private $genreRepository;

    public function __construct(GenreRepository $genreRepository) {
        $this->genreRepository = $genreRepository;
    }

    public function getAll(): array {
            $genres = $this->genreRepository->all();
            if ($genres->isEmpty()) {
                return ["success" => false, "message" => "No Genres found"];
            }
            return ["success" => true, "data" => $genres];
        

    }

    public function find(int $id): array {
        return $this->genreRepository->find($id);
        
    }

    public function create(array $data): array {
        $validator = ValidationRules::validate($data, ValidationRules::$genreStoreRule);
        
        // if ($this->genreRepository->exists(["name"=> $data["name"]])) {
        //     return ["success" => false, "message" => "Genre already exists"];
        // }

        try {
            $genre = $this->genreRepository->create($data);
            return ["success" => true, "Genre" => $genre];
        } catch (Exception $e) {
            Log::error("Failed to create genre: {$e->getMessage()}", ['exception' => $e]);
            throw new \RuntimeException("Failed to create Genre", 0, $e);
        }
    }

    public function delete(int $id): array {
        $genre = $this->genreRepository->find($id);
        // if (!$genre) {
        //     return ["success" => false, "message" => "Genre not found"];
        // }

        // try {
            $result = $this->genreRepository->delete($id);
            // if ($result) 
                return ["success" => true, "message" => "Genre deleted successfully"];
            
        // } catch (Exception $e) {
        //     Log::error("Failed to delete genre: {$e->getMessage()}", ['exception' => $e]);
        //     throw new \RuntimeException("Failed to delete Genre", 0, $e);
        // }
    }

    public function update(int $id, array $data): array {

        $validator = ValidationRules::validate($data, ValidationRules::$genreUpdateRule);
        

        $genre = $this->genreRepository->find($id);
        // if (!$genre) {
        //     return ["success" => false, "message" => "Genre not found"];
        // }

        // try {
            $this->genreRepository->update($id, $data);
            return ["success" => true, "message" => "Genre updated successfully"];
        // } catch (Exception $e) {
        //     Log::error("Failed to update genre: {$e->getMessage()}", ['exception' => $e]);
        //     throw new \RuntimeException("Failed to update Genre", 0, $e);
        // }
    }
}
