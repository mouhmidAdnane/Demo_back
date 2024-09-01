<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;



class AuthController extends Controller
{


    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function register(Request $request){

        try {

            $data = $request->only(['first_name', 'last_name', 'phone', 'email', 'password', 'c_password']);
            $result = $this->userService->regiser($data);
            $status = $result['success'] ? 201 : 400;
            return response()->json($result, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error("Failed to register user: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function login(Request $request){
        try {
            $data = $request->only(['email', 'password']);
            $result = $this->userService->login($data);


            $token= $result["token"];
            // unset($result["token"]);
            $status = $result['success'] ? 200 : 401;

            return response()->json($result, 200)->cookie('token', $token, 60, '/', null, false, true);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error("Failed to login user: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json([
            'success' => true,
            'message' => 'You have been successfully logged out.'
        ], 200);
    }
}
