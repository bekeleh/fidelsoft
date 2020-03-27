<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{

    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            Session::reflash();
            switch ($guard) {
                case 'client':
                    if (session('contact_key')) {
                        return redirect('/client/dashboard');
                    }
                    break;
                default:
                    return redirect('/dashboard');
                    break;
            }
        }

        return $next($request);
    }
}
