<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class roleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response{
        if(!Auth::guard('api')->check()){
            return response()->json(["error"=> "unauthorized"], 403);
        }

        if (empty($permission)) {
            return $next($request);
        }

        foreach($permissions as $permission){
            
            if(Auth::guard('api')->user()->hasPermissionTo($permission)){
                return $next($request);
            }
            
        }
        return response()->json(["error"=> "unauthorized"], 403);

    }
}
