<?php

namespace App\Utils;

use Illuminate\Validation\ValidationException;
use Validator;
use Illuminate\Validation\Rule;

class ValidationRules{
    public static $userStoreRules= [
        "first_name" => "required|string|max:50", 
        "last_name" => "required|string|max:50", 
        "phone" => "required|numeric|digits_between:10,15|unique:users,phone", 
        "email" => "required|email|unique:users,email",
        "password" => "required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 
        "c_password" => "required|same:password", 
        "roles" => "nullable|array", 
        "roles.*" => "string|exists:roles,name"
    ];

    // public static $userUpdateRules= [
    //     "first_name" => "string|max:50", 
    //     "last_name" => "string|max:50", 
    //     "phone" => "numeric|digits_between:10,15", 
    //     "email" => "email|unique:users,email",
    //     "password" => "string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 
    //     'c_password'=> 'required_if:password|required|same:password'
    // ];

    public static function userUpdateRules($userId) {
        return [
            "first_name" => "string|max:50", 
            "last_name" => "string|max:50", 
            "phone" => "numeric|digits_between:10,15|unique:users,phone," . $userId, 
            "email" => "email|unique:users,email," . $userId,
            "password" => "string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 
            'c_password'=> 'required_with:password|same:password',
            "roles" => "nullable|array",
            "roles.*" => "string|exists:roles,name"
        ];
    }

    public static $userLoginRules= [
        "email" => "required|email",
        "password" => "required|string"
    ];

    public static $roleStoreRules= [
        "name" => "required|max:125|unique:roles",
        'description' => 'max:511',
        'permissions' => 'nullable|array',
        'permissions.*'=>'string|exists:permissions,name'
    ];

    public static $roleUpdateRules= [
        'name' => 'max:125|unique:roles',
        'description' => 'max:511',
        'permissions' => 'nullable|array',
        'permissions.*'=>'string|exists:permissions,name'
    ];

    public static $permissionIdRule= [
        'permission' => 'required|numeric'
    ];
    public static $roleIdRule= [
        'role' => 'required|numeric'
    ];

    public static $permissionStoreRule=  [
        "name" => "required|max:125|unique:permissions",
        "description" => 'max:511'
    ];

    public static $permissionUpdateRule=   [
        'name' => 'max:125|unique:permissions',
        'description' => 'max:511'
    ];

    public static $genreStoreRule=  [
        "name" => "required|max:125|unique:genres",
        "description" => 'max:511'
    ];

    public static $genreUpdateRule=  [
        "name" => "max:125|unique:genres",
        "description" => 'max:511'
    ];


    public static function authorStoreRule(){
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'profile_picture' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'birthday' => 'required|date|date_format:Y-m-d',
            'nationality' => ['required', Rule::in(nationalities::$nationalities)], 
        ];
    } 

    public static function authorUpdateRule(){
        return [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'profile_picture' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'birthday' => 'date|date_format:Y-m-d',
            'nationality' => [Rule::in(nationalities::$nationalities)], 
        ];
    } 

    public static $bookStoreRule= [
        'title' => 'required|string|max:255',
        'author_id' => 'required|exists:authors,id',
        'genre_id' => 'nullable|exists:genres,id',
        'publish_date' => 'required|date|date_format:Y-m-d',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        'summury' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|max:100', 
    ];

    public static $bookUpdateRule= [
        'title' => 'string|max:255',
        'author_id' => 'exists:authors,id',
        'genre_id' => 'exists:genres,id',
        'publish_date' => 'date|date_format:Y-m-d',
        'cover_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        'summury' => 'string',
        'price' => 'numeric|min:0',
        'discount' => 'numeric|min:0|max:100', 
    ];


    public static function validate(array $data, array $rules)
{
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        throw new ValidationException($validator);

    }

    return $validator;
}
}