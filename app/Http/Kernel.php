<?php

namespace App\Http;


use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware permission_groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \App\Http\Middleware\DuplicateSubmissionCheck::class,
            \App\Http\Middleware\QueryLogging::class,
            \App\Http\Middleware\StartupCheck::class,
            \App\Http\Middleware\CheckBanned::class,
        ],

        'api' => [
            \App\Http\Middleware\QueryLogging::class,
            \App\Http\Middleware\ApiCheck::class,
        ],
        /*
        'api' => [
            'throttle:120,1',
            'auth:api',
        ],
        */
    ];

    /**
     *
     * The application's route middleware.
     *
     * These middleware may be assigned to permission_groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'lookup' => \App\Http\Middleware\DatabaseLookup::class,
        'permissions.required' => \App\Http\Middleware\PermissionsRequired::class,
        'migration' => \App\Http\Middleware\EligibleForMigration::class,
    ];

    /**
     *
     * The priority-sorted list of middleware.
     *
     * this forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,

    ];
    
}
