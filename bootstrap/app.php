<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'dosen' => \App\Http\Middleware\EnsureDosenRole::class,
            'admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin_prodi' => \App\Http\Middleware\AdminProdiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
