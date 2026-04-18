<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAdminRole;      // ✅ Нэмэх
use App\Http\Middleware\CheckCustomerRole;  // ✅ Нэмэх

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'customer' => CheckCustomerRole::class,  // ✅ Customer middleware нэмэх
            'admin' => CheckAdminRole::class,        // ✅ Admin middleware нэмэх
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();