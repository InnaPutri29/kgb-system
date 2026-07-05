<?php

use App\Console\Commands\CheckKgbDue;
use Illuminate\Support\Facades\Schedule;
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
        // Alias Middleware untuk digunakan di routes
        $middleware->alias([
            'role'             => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'       => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'password.changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Jalankan setiap hari pukul 06:00 pagi
        $schedule->command('kgb:check-due')->dailyAt('06:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

