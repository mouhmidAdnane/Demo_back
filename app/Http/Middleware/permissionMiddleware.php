<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class permissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response{

        if (empty($permissions))
            return $next($request);
        

        $user = Auth::guard('api')->user();
        foreach($permissions as $permission){
            if($user->hasPermissionTo($permission))
                return $next($request);
        }
        return response()->json(["error"=> "unauthorized"], 403);
    }
}
