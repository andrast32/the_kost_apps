<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // untuk remeber me
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->redirectUsersTo(function () {
            $user = Auth::user();

            if ($user && ($user->role === 'Admin')) {
                return route('admin.dashboard');
            }

            return route('dashboard');

        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
