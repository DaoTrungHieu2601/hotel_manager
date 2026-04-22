<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('bookings:release-no-shows')->everyFifteenMinutes();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'system_portal' => \App\Http\Middleware\SystemPortalMiddleware::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
