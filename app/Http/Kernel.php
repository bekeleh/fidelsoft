<?php

namespace App\Http;


use App\Http\Middleware\ApiCheck;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckBanned;
use App\Http\Middleware\DatabaseLookup;
use App\Http\Middleware\DuplicateSubmissionCheck;
use App\Http\Middleware\EligibleForMigration;
use App\Http\Middleware\EligibleForSetup;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\PermissionsRequired;
use App\Http\Middleware\QueryLogging;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\StartupCheck;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\TrimStrings;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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
        CheckForMaintenanceMode::class,
        ConvertEmptyStringsToNull::class,
        ValidatePostSize::class,
        TrimStrings::class,
    ];

    /**
     * The application's route middleware permission_groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            TrimStrings::class,
            DuplicateSubmissionCheck::class,
            QueryLogging::class,
            StartupCheck::class,
        ],

        'api' => [
            QueryLogging::class,
            ApiCheck::class,
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
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,
        'lookup' => DatabaseLookup::class,
        'permissions.required' => PermissionsRequired::class,
        'migration' => EligibleForMigration::class,
        'banned' => CheckBanned::class,
        'setup' => EligibleForSetup::class,
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
        StartSession::class,
        ShareErrorsFromSession::class,
        Authenticate::class,
        AuthenticateSession::class,
        SubstituteBindings::class,
        Authorize::class,

    ];

}
