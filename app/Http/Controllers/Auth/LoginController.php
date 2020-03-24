<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTwoFactorRequest;
use App\Libraries\Utils;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use ThrottlesLogins;

    protected $username = 'username';
    protected $redirectTo = '/dashboard';

    private $maxLoginAttempts;
    private $lockoutTime;


    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogoutWrapper']);
        Session::put('backUrl', URL::previous());
    }

    public function redirectTo()
    {
        return Session::get('backUrl') ? Session::get('backUrl') : $this->redirectTo;
    }

    public function username()
    {
        return 'username';
    }

    function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended('dashboard');
        }
        return view('auth.login');
    }

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
            $subDomain = Utils::getSubdomain(request()->url());
            if ($requestURL != $loginURL && !strstr($subDomain, 'webapp-')) {
                return redirect()->to($loginURL);
            }
        }

        return self::showLoginForm($request);
    }

    public function legacyAuthRedirect()
    {
        return redirect()->route('login');
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        $lockoutTime = config('auth.throttle.lockout_duration');
        $maxLoginAttempts = config('auth.throttle.max_attempts');

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $maxLoginAttempts, $lockoutTime
        );
    }


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

    public function postLoginWrapper(Request $request)
    {
        $validator = $this->validator($request->all());
        dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $this->maxLoginAttempts = config('auth.throttle.max_attempts');
        $this->lockoutTime = config('auth.throttle.lockout_duration');
        dd($this->maxLoginAttempts);

        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        dd($request->input('remember'));
        $auth = Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password'), 'activated' => 1], $request->input('remember'));
        if (!$auth) {
            if (!$lockedOut) {
                $this->incrementLoginAttempts($request);
            }
            Log::debug("Authentication failed.");
            return redirect()->back()->withInput()->with('error', trans('admin/auth/message.account_not_found'));
        } else {
            $this->clearLoginAttempts($request);
        }
        if ($user = Auth::user()) {
            $user->last_login = Carbon::now();
            $user->save();
        }
        // Redirect to the users page
        return redirect()->intended()->with('success', trans('admin/auth/message.signin.success'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('texts.invalid_credentials'),
            ]);
    }

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

    public function getValidateToken()
    {
        if (session('2fa:user:id')) {
            return view('auth.two_factor');
        }

        return redirect('login');
    }

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
