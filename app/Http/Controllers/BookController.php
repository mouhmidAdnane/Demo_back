<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BookController extends Controller
{
    private $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(): JsonResponse
    {
        try{
        $books= $this->bookService->getAll();
            // $status= $books["success"] ? 200 : 422;
            return response()->json($books, 200);
        }catch(Exception $e){
            Log::error("failed to get books: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->bookService->create($data);
            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->bookService->find($id);
            return response()->json($result, 200);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
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
            $data = $request->all();
            $result = $this->bookService->update($id, $data);
            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
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
            $result = $this->bookService->delete($id);
            return response()->json($result, 200);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

   

