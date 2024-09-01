<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    public function permissions(){
        return $this->hasMany(Permission::class);
    }

    public static $storeRules= [
        "name" => "required|max:125|unique:roles",
        'description' => 'max:511'
    ];

    use HasFactory;
}
