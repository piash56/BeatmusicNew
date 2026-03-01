<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', \App\Http\Middleware\IncreaseUploadLimit::class);
        $middleware->alias([
            'is_admin'      => \App\Http\Middleware\EnsureAdmin::class,
            'verified_user' => \App\Http\Middleware\EnsureVerified::class,
        ]);

        // Redirect authenticated users: admins → admin dashboard, artists → dashboard
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.dashboard');
            }
            return route('dashboard.home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Please log in to access the admin panel.');
            }
        });
    })->create();
