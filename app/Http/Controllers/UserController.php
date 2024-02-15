<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
// use App\Models\Role;
use App\Models\User;
// use Spatie\Permission\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class UserController extends Controller
{
    public function assignRole(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'role' => 'required|max:125|exists:roles,name'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()], 422);
        }

        $user = User::find($id)->first();
        if ($user == null) {
            return response()->json([
                'message'  => "user not found",
            ], 422);
        }

        $user->assignRole($request->input("role"));
        return response()->json([
        'message' => 'Role assigned successfully',
        ], 200);
    }

    public function revokeRole(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'role' => 'required|max:125|exists:roles,name'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()], 422);
        }
       
        $user = User::find($id);
        $role = Role::findByName($request->input("role"));

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } else {
            $user->removeRole($role);
            return response()->json([
                'message' => 'Role revoked successfully'
            ], 200);
        }
    }
}
