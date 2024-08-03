<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()){
            $role=Auth::user()->role->role_name;
            $role=strtolower($role);
            if ($role=="admin") {
                return $next($request);
            }
            abort(403);
        }else{
            return redirect()->route('login');;
        }

       
    }
}
