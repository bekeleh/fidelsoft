<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Ninja\Mailers\InvoiceMailer;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class ForceResetPasswordController extends Controller
{
    private $userRepo;
    protected $accountRepo;
    protected $contactMailer;
    protected $userMailer;
    protected $userService;

    public function __construct(InvoiceMailer $userMailer, UserService $userService)
    {
        //parent::__construct();

        $this->userMailer = $userMailer;
        $this->userService = $userService;
    }

    public function showUserForPasswordReset($publicId = null)
    {
        if (!auth()->user()->registered) {
            return response()->view('errors/404');
        }

        $data = [
            'user' => User::with('account')->where('account_id', Auth::user()->account_id)->where('public_id', $publicId)->first(),
            'title' => trans('texts.user_details'),
        ];

        return View::make('auth.forceResetPassword', $data);
    }

    public function changePassword()
    {
        $publicId = Input::get('public_id');

        if (!$publicId) {
            return trans('texts.password_error_invalid');
        }
        $user = User::where('account_id', Auth::user()->account_id)->where('public_id', $publicId)->first();

        // validate the new password
        $password = Input::get('new_password');
        $confirm = Input::get('confirm_password');

        if (strlen($password) < 6 || $password != $confirm) {
            return trans('texts.password_error_invalid');
        }

        $user->password = bcrypt($password);

        if ($user->save()) {
            return RESULT_SUCCESS;
        }

        return RESULT_FAILURE;

    }


    public function switchAccount($newUserId)
    {
        $oldUserId = Auth::user()->id;
        $referer = Request::header('referer');
        $account = $this->accountRepo->findUserAccounts($newUserId, $oldUserId);

        if ($account) {
            if ($account->hasUserId($newUserId) && $account->hasUserId($oldUserId)) {
                Auth::loginUsingId($newUserId);
                Auth::user()->account->loadLocalizationSettings();

                // regenerate token to prevent open pages
                // from saving under the wrong account
                Session::put('_token', str_random(40));
            }
        }

        // If the user is looking at an entity redirect to the dashboard
        preg_match('/\/[0-9*][\/edit]*$/', $referer, $matches);
        if (count($matches)) {
            return Redirect::to('/dashboard');
        } else {
            return Redirect::to($referer);
        }
    }


    private static function getViewModel($user = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }

}