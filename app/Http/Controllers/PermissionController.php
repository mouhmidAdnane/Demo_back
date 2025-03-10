<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionController extends Controller
{
    private $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(): JsonResponse
    {
        try{
        $permissions= $this->permissionService->getAll();
            $status= $permissions["success"] ? 200 : 404;
            return response()->json($permissions, $status);
        }catch(Exception $e){
            return response()->json(['message' => 'Server error',500]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description']); // Adjust based on your form fields
            $result = $this->permissionService->create($data);
            $status= $result["success"] ? 200 : 401;
            return response()->json($result, $status);
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
            $result = $this->permissionService->find($id);
            return response()->json($result, $result['success'] ? 200 : 404);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified permission in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description']); // Adjust based on your form fields
            $result = $this->permissionService->update($id, $data);
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

    /**
     * Remove the specified permission from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->permissionService->delete($id);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
