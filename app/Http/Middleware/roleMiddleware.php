<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Traits\HasRoles;

class roleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(empty($roles))
            return $next($request);

        $user = Auth::guard('api')->user();
        foreach ($roles as $role) {
            if ($user->hasRole($role)) 
                return $next($request);   
        }
        
        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
