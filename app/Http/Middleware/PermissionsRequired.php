<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionsRequired.
 */
class PermissionsRequired
{
    protected static $actions = [];

    public function handle(Request $request, Closure $next, $guard = 'web', $section = null)
    {
        // Get the current route.
        $route = $request->route();

        // Get the current route actions.
        $actions = $route->getAction();

        // Check if we have any permissions to check the user has.
        $permissions = !empty($actions['permissions']) ? $actions['permissions'] : null;
        // Check if we have any permissions to this section
        $section = !empty($actions['as']) ? $actions['as'] : null;

        if ($section) {
            if (!Auth::user($guard)->hasAccess($section)) {
                return response()->view('errors/403');
            }
        }

        return $next($request);
    }

    public static function addPermission(Controller $controller, array $permissions)
    {
        static::$actions[get_class($controller)] = $permissions;
    }
}
