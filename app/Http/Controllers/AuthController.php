<?php

namespace App\Http\Controllers;


use Exception;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 



class AuthController extends Controller
{
    public function register(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                "first_name" => "required",
                "last_name" => "required",
                "phone" => "required",
                "email" => "required|email",
                "password" => "required",
                "c_password" => "required|same:password"
            ]);
    
            if($validator->fails())
                return response()->json(["message"=>$validator->errors()], 401);
            
            if(Auth::attempt(["email" => request("email"), "password"=>request("password")]))
                return response()->json(["message"=>"User aleary exist!", 401]);
            
            $data= $request->all();
            $data["password"]= bcrypt($data["password"]);
            $user= User::create($data);
            $success["token"]= $user->createToken("restaurant")->accessToken;
            return response()->json(['success'=>$success], 200); 
        }catch(Exception $e){
            Log::error("Failed to register: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
        }

    }

    public function login(){
        try{
            $validator = Validator::make(
                ['email' => request('email'), 'password' => request('password')],[
                    'email' => 'required|email',
                    'password' => 'required',
                ],
            );
            if ($validator->fails()) 
                return response()->json(['message' => $validator->errors()], 401);
            if(!Auth::attempt(['email' => request('email'), 'password' => request('password')]))
                return response()->json(['messages' => 'Unauthorised'], 401);
    
                    $user = Auth::user();
                    return response()->json([
                        'messages' => 'success',
                        'token' => $user->createToken('restaurant')->accessToken
                    ], 200);

        }catch(Exception $e){
            Log::error("Failed to Log in: {$e->getMessage()}");
            return response()->json(['message' => 'Server error', 'error'=>$e,500]);
    }
}
}