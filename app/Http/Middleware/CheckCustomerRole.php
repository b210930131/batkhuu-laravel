<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCustomerRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'customer') {
            return $next($request);
        }
        
        // Админ хэрэглэгчийг админ панел руу чиглүүлэх
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        
        return redirect('/login');
    }
}
