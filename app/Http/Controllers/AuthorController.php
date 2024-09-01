<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\AuthorServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorController extends Controller
{
    private $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    public function index(): JsonResponse
    {
        try{
        $authors= $this->authorService->getAll();
            return response()->json($authors, 200);
        }catch(Exception $e){
            Log::error("failed to get authors: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e->getMessage(),500]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all(); 
            $result = $this->authorService->create($data);
            $status= $result["success"] ? 200 : 401;
            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->authorService->find($id);
            return response()->json($result, $result['success'] ? 200 : 404);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Server error",
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->all(); 
            $result = $this->authorService->update($id, $data);
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->authorService->delete($id);
            return response()->json($result, $result['success'] ? 200 : 404);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}