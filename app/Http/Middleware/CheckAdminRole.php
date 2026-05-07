<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }
        
        // Customer хэрэглэгчийг customer панел руу чиглүүлэх
        if (auth()->check() && auth()->user()->role === 'customer') {
            return redirect('/customer/dashboard');
        }
        
        return redirect('/login');
    }
}
