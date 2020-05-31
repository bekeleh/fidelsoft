<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Contact;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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

    use AuthenticatesUsers;


    protected $redirectTo = '/client/dashboard';

    public function __construct()
    {
        $this->middleware('guest:client', ['except' => 'getLogoutWrapper']);
    }


    protected function guard()
    {
        return auth()->guard('client');
    }

    public function showLoginForm()
    {
        $subdomain = Utils::getSubdomain(\Request::server('HTTP_HOST'));

        $hasAccountIndentifier = request()->account_key || ($subdomain && !in_array($subdomain, ['www', 'app']));

        if (!session('contact_key')) {
            if (Utils::isNinja()) {
                if (!$hasAccountIndentifier) {
                    return redirect('/client/session_expired');
                }
            } else {
                if (!$hasAccountIndentifier && Account::count() > 1) {
                    return redirect('/client/session_expired');
                }
            }
        }

        return view('clientauth.login')->with(['clientauth' => true]);
    }

    protected function credentials(Request $request)
    {
        if ($contactKey = session('contact_key')) {
            $credentials = $request->only('password');
            $credentials['contact_key'] = $contactKey;
        } else {
            $credentials = $request->only('email', 'password');
            $email = $request->input('email');
            $password = bcrypt($request->input('password'));
            $auth = Contact::where('email', $email)->first();
            if ($auth) {
                $account = $auth->account;
                // resolve the email to a contact/account
//            if (!Utils::isNinja() && Account::count() == 1) {
//                $account = Account::first();
//            } elseif ($accountKey = request()->account_key) {
//                $account = Account::whereAccountKey($accountKey)->first();
//            } else {
//                $subdomain = Utils::getSubdomain(\Request::server('HTTP_HOST'));
//                if ($subdomain && $subdomain != 'app') {
//                    $account = Account::whereSubdomain($subdomain)->first();
//                }
//            }

                if ($account) {
                    $credentials['account_id'] = $account->id;
                }
            } else {
//                return to login page
                abort(500, 'Account not resolved in client login');
            }
        }

        return $credentials;
    }

    private function authenticated(Request $request, Authenticatable $contact)
    {
        session(['contact_key' => $contact->contact_key]);

        return redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('texts.invalid_credentials'),
            ]);
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            'password' => 'required',
        ];

        if (!session('contact_key')) {
            $rules['email'] = 'required|email';
        }

        $this->validate($request, $rules);
    }

    public function getSessionExpired()
    {
        return view('clientauth.sessionexpired')->with(['clientauth' => true]);
    }

    public function getLogoutWrapper(Request $request)
    {
        $contactKey = session('contact_key');

        self::logout($request);

        return redirect('/client/dashboard/' . $contactKey);
    }

}
