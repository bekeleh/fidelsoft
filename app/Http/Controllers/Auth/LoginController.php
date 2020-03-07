<?php

namespace App\Http\Controllers\Auth;

use App\Libraries\Utils;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Events\UserLoggedIn;
use App\Http\Requests\ValidateTwoFactorRequest;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
//    use ThrottlesLogins;
    use AuthenticatesUsers;


    protected $username = 'username';
    protected $redirectTo = '/dashboard';


    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogoutWrapper']);
        Session::put('backUrl', URL::previous());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getLoginWrapper(Request $request)
    {
        if (auth()->check()) {
            return redirect('/');
        }

        if (!Utils::isNinja() && !User::count()) {
            return redirect()->to('/setup');
        }

        if (Utils::isNinja() && !Utils::isTravis()) {
            // make sure the user is on SITE_URL/login to ensure OAuth works
            $requestURL = request()->url();
            $loginURL = SITE_URL . '/login';
            $subdomain = Utils::getSubdomain(request()->url());
            if ($requestURL != $loginURL && !strstr($subdomain, 'webapp-')) {
                return redirect()->to($loginURL);
            }
        }

        return self::showLoginForm($request);
    }

    public function username()
    {
        return 'username';
    }

    public function legacyAuthRedirect()
    {
        return redirect()->route('login');
    }

    public function redirectTo()
    {
        return Session::get('backUrl') ? Session::get('backUrl') : $this->redirectTo;
    }

    /**
     * Override the lockout time and duration
     *
     * @param Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $lockoutTime = config('auth.throttle.lockout_duration');
        $maxLoginAttempts = config('auth.throttle.max_attempts');

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $maxLoginAttempts,
            $lockoutTime
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $minutes = round($seconds / 60);

        $message = Lang::get('auth/message.throttle', ['minutes' => $minutes]);

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => $message]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postLoginWrapper(Request $request)
    {
        $userId = auth()->check() ? auth()->user()->id : null;
        $user = User::where('username', '=', $request->input('username'))->first();

        if ($user && $user->failed_logins >= MAX_FAILED_LOGINS) {
            session()->flash('error', trans('texts.invalid_credentials'));
            return redirect()->to('login');
        }

        $response = self::login($request);

        if (auth()->check()) {
            /*
            $users = false;
            // we're linking a new account
            if ($request->link_accounts && $userId && Auth::user()->id != $userId) {
                $users = $this->accountRepo->associateAccounts($userId, Auth::user()->id);
                Session::flash('message', trans('texts.associated_accounts'));
                // check if other accounts are linked
            } else {
                $users = $this->accountRepo->loadAccounts(Auth::user()->id);
            }
            */
        } else {
            $stacktrace = sprintf("%s %s %s %s\n", date('Y-m-d h:i:s'), $request->input('username'), Request::getClientIp(), array_get($_SERVER, 'HTTP_USER_AGENT'));
            if (config('app.log') == 'single') {
                file_put_contents(storage_path('logs/failed-logins.log'), $stacktrace, FILE_APPEND);
            } else {
                Utils::logError('[failed login] ' . $stacktrace);
            }
            if ($user) {
                $user->failed_logins = $user->failed_logins + 1;
                $user->save();
            }
        }

        return $response;
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('texts.invalid_credentials'),
            ]);
    }

    /**
     * Send the post-authentication response.
     *
     * @param Request $request
     * @param Authenticatable $user
     * @return Response
     */
    private function authenticated(Request $request, Authenticatable $user)
    {
        if ($user->google_2fa_secret) {
            $cookie = false;
            if ($user->remember_2fa_token) {
                $cookie = Cookie::get('remember_2fa_' . sha1($user->id));
            }

            if ($cookie && hash_equals($user->remember_2fa_token, $cookie)) {
                // do nothing
            } else {
                auth()->logout();
                session()->put('2fa:user:id', $user->id);
                return redirect('/validate_two_factor/' . $user->account->account_key);
            }
        }

        Event::fire(new UserLoggedIn());

        return redirect()->intended($this->redirectTo);
    }

    /**
     *
     * @return Response
     */
    public function getValidateToken()
    {
        if (session('2fa:user:id')) {
            return view('auth.two_factor');
        }

        return redirect('login');
    }

    /**
     *
     * @param ValidateTwoFactorRequest $request
     * @return Response
     */
    public function postValidateToken(ValidateTwoFactorRequest $request)
    {
        //get user id and create cache key
        $userId = session()->pull('2fa:user:id');
        $key = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);

        //login and redirect user
        auth()->loginUsingId($userId);
        Event::fire(new UserLoggedIn());

        if ($trust = request()->trust) {
            $user = auth()->user();
            if (!$user->remember_2fa_token) {
                $user->remember_2fa_token = Str::random(60);
                $user->save();
            }
            $minutes = $trust == 30 ? 60 * 27 * 30 : 2628000;
            cookie()->queue('remember_2fa_' . sha1($user->id), $user->remember_2fa_token, $minutes);
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getLogoutWrapper(Request $request)
    {
        if (auth()->check() && !auth()->user()->username && !auth()->user()->registered) {
            if (request()->force_logout) {
                $account = auth()->user()->account;
                app('App\Ninja\Repositories\AccountRepository')->unlinkAccount($account);

                if (!$account->hasMultipleAccounts()) {
                    $account->company->forceDelete();
                }
                $account->forceDelete();
            } else {
                return redirect('/');
            }
        }

        $response = self::logout($request);

        $reason = htmlentities(request()->reason);
        if (!empty($reason) && Lang::has("texts.{$reason}_logout")) {
            session()->flash('warning', trans("texts.{$reason}_logout"));
        }

        return $response;
    }
}
