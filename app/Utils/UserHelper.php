<?php


namespace App\Utils;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class UserHelper{


    public static function hashPassword($password){
        return bcrypt($password);
    }

    public static function userExists($email){
        return User::where("email", $email)->exists();
    }

    // public static function emailOrPhoneExists($email, $phone){
    //     $user= User::where("email", $email)
    //     ->orWhere("phone", $phone)->first();

    //     if ($user) {
    //         if (isset($user->email)) {
    //             return ["exists" => true, "message" => "email"];
    //         } elseif ($user->phone === $phone) {
    //             return "phone";
    //         }
    //     }

    // }

    public static function attemptLoging( array $data){
        if(!Auth::attempt(['email' => $data['email'], 'password' => $data['password']]))
                return false;
        return true;
    }

}
