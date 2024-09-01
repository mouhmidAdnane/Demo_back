<?php

namespace App\Services;

use App\Models\User;
use Validator;

class UserService{

    public function createUser(array $data):array{
        $validator = Validator::make($data, User::$storeRules);

        if($validator->fails())
            return ["success"=> false,  "message"=>$validator->errors()];

        if(!User::where("email", $data["email"])->exists());
            return ["success"=> false,  "message"=>"User aleary exist!"];
            
        $data["password"]= bcrypt($data["password"]);
        $user= User::create($data);
        if(!$user)
            throw new \Exception("Failed to create user");
        $success["token"]= $user->createToken("restaurant")->accessToken;
        return ["success"=>$success];
        
    }

    public function updateUser(User $userId, array $data)
    {
        $validator = Validator::make($data,User::$updateRules);
            
            if ($validator->fails())
                return ["success"=> false,  "message"=>$validator->errors()];
            
            $user= User::find($userId)->first();
            if ($user === null) 
                return ["success"=> false,"message" => "User not found"];
            
            if($data["password"] != null) 
                $data["password"]= bcrypt($data["password"]);

            $user->update($data);
            return ["success"=> true,  "message"=>"user updated successfully"];
    }

    public function deleteUser(User $userId):array
    {
        $user = User::find($userId)->first();
            if ($user == null) 
                return ["success"=> false,  "message"=>"user not found"];
            $user->delete();
            return ["success"=> true,  "message"=>"user deleted successfully"];
    }

    public function getUserById($userId)
    {
        // Logic to fetch user by ID
    }

    public function getAllUsers():array
    {
        $users = User::all();
            if (empty($users)) 
                return ["success"=> false,  "message"=>"no users found"];
            return ["success"=> true,  "data"=>$users];
    }
}


