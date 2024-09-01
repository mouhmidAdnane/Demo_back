<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static $storeRules= [
        "first_name" => "required",
        "last_name" => "required",
        "phone" => "required",
        "email" => "required|email",
        "password" => "required",
        "c_password" => "required|same:password"
    ];

    public static $updateRules= [
        'first_name' => 'max:125',
        'last_name' => 'max:125',
        'email' => 'max:125|email|unique:users,email',
        'phone' => 'max:125',
        'password' => 'max:255',
        'c_password'=> 'required_if:password|required|same:password'
    ];

    public function setPassword($value){
        $this->attributes["password"] = bcrypt($value);
    }

    public static function userExists($email){
        return Auth::attempt(["email" => $email, "password"=>$password]);
    }
}
