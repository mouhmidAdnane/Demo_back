<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $result = $this->userService->create($data);
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

    public function update(Request $request, $userId)
    {
        try {
            $data = $request->all();
            $result = $this->userService->update($userId, $data);
            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "server error",
            ], 500);
        }
    }

    public function destroy($userId)
    {
        try {
            $result = $this->userService->delete($userId);
            return response()->json($result, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function show($userId)
    {
        try {
            $result = $this->userService->getUserInformation($userId);

            $status = $result['success'] ? 200 : 404;
            return response()->json($result, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } 
        // catch (Exception $e) {
        //     return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
        // }
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', null);

        try {
            $result = $this->userService->getAll($perPage, $page);
            if (!$result['success']) {
                return $result;
            }

            if (isset($result["metadata"])) {
                $metadata = $result["metadata"];
                unset($result["metadata"]);
            }
            $response = response()->json($result, 200);

            if (isset($metadata)) {
                $response->headers->set('X-Total-Items', $metadata['total']);
                $response->headers->set('X-Current-Page', $metadata['current_page']);
                $response->headers->set('X-Last-Page', $metadata['last_page']);
                $response->headers->set('X-Per-Page', $metadata['per_page']);
                $response->headers->set('X-From', $metadata['from']);
                $response->headers->set('X-To', $metadata['to']);
            }

            return $response;
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
        }
    }



    public function assignRole(Request $request, $userId)
    {

        try {
            $data = $request->query("role");
            $result = $this->userService->assignRole($userId, $data);
            // $status = $result['success'] ? 200 : 404;
            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function revokeRole(Request $request, int $userId)
    {

        $data = $request->query("role");

        try {
            $result = $this->userService->revokeRole($userId, $data);
            $status = $result['success'] ? 200 : 400;
            return response()->json($result, $status);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignPermission(Request $request, int  $userId)
    {

        $permissionRepository = app(PermissionRepository::class);
        try {
            $data = $request->query("permission");
            $result = $this->userService->assignPermission($permissionRepository, $userId, $data);
            $status = $result['success'] ? 200 : 400;
            return response()->json($result, $status);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function revokePermission(Request $request, $userId)
    {

        $data = $request->query("permission");
        $permissionRepository = app(PermissionRepository::class);

        try {
            $result = $this->userService->revokePermission($permissionRepository, $userId, $data);
            $status = $result['success'] ? 200 : 400;
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
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
