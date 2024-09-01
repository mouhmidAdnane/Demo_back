<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\RoleServiceInterface;
use App\Repositories\PermissionRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    private $roleService;
    // private $permissionService;

    public function __construct(RoleService $roleService, PermissionRepository $permissionService)
    {
        $this->roleService = $roleService;
        // $this->permissionService = $permissionService;
    }

    public function index(Request $request): JsonResponse
    {
        $namesOnly = $request->query('names_only', false) ? true : false;
        
        try {

            $roles = $this->roleService->getAll($namesOnly);
            return response()->json($roles, 200);
        } catch (Exception $e) {
            Log::error("Failed to get roles: {$e->getMessage()}");
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->roleService->create($data);
            $status = $result["success"] ? 201 : 400;
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->roleService->find($id);
            return response()->json($result, $result['success'] ? 200 : 404);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->all(); // Adjust based on your form fields
            $result = $this->roleService->update($id, $data);
            return response()->json($result, $result['success'] ? 200 : 400);
        }
        catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } 
        catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->roleService->delete($id);
            return response()->json($result, $result['success'] ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function assignPermission(Request $request, $roleId){

        try {
            $data= $request->query("permission");
            $result = $this->roleService->assignPermission($roleId, $data);

            $status = $result['success'] ? 200 : 404;
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function revokePermission(Request $request, int $roleId){

        $data= $request->query("permission");
        
        try {
            $result = $this->roleService->revokePermission($roleId, $data);

            if ($result['success']) 
                return response()->json($result, 200);

            return response()->json($result, 400);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
