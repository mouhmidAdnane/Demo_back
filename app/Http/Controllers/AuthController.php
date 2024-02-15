<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Validator;


class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "required",
            "phone" => "required",
            "email" => "required|email",
            "password" => "required",
            "c_password" => "required|same:password"
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()], 401);
        }elseif(Auth::attempt(["email" => request("email"), "password"=>request("password")])){
            return response()->json(["message"=>"User aleary exist!", 401]);
        }else{
            $data= $request->all();
            $data["password"]= bcrypt($data["password"]);
            $user= User::create($data);
            $success["token"]= $user->createToken("restaurant")->accessToken;
            return response()->json(['success'=>$success], 200); 
        }
    }

    public function login()
    {
        $validator = Validator::make(
            ['email' => request('email'), 'password' => request('password')],
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 401);
        } else {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                return response()->json([
                    'messages' => 'success',
                    'token' => $user->createToken('restaurant')->accessToken
                ], 200);
            } else {
                return response()->json(['messages' => 'Unauthorised'], 401);
            }
        }
    }

}
