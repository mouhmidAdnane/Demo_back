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
    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response{
        if(!Auth::guard('api')->check()){
            return response()->json(["error"=> "unauthorized"], 403);
        }

        if (empty($rolesOrPermissions)) {

            // dd($rolesOrPermissions);
            return $next($request);
        }

        foreach($rolesOrPermissions as $rolesOrPermission){
            
            if(Auth::guard('api')->user()->hasRole($rolesOrPermission)){
                return $next($request);
            }
            
        }
        return response()->json(["error"=> "unauthorized"], 403);

    }
}
