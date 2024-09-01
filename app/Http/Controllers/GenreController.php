<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\GenreServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenreController extends Controller
{
    private $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    public function index(): JsonResponse
    {
        try{
        $genres= $this->genreService->getAll();
        return response()->json($genres, 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Server error',500]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description']);
            $result = $this->genreService->create($data);
            $status= $result["success"] ? 200 : 401;
            return response()->json($result, $status);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "server error",
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->genreService->find($id);
            return response()->json($result, 200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Genre not found',
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description']);
            $result = $this->genreService->update($id, $data);
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Genre not found',
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->genreService->delete($id);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
