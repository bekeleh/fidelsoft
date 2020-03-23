<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Libraries\Utils;
use App\Models\User;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class UserController extends BaseController
{
    protected $accountRepo;
    protected $contactMailer;
    protected $userMailer;
    protected $userService;

    public function __construct(AccountRepository $accountRepo, ContactMailer $contactMailer, UserMailer $userMailer, UserService $userService)
    {
        //parent::__construct();

        $this->accountRepo = $accountRepo;
        $this->contactMailer = $contactMailer;
        $this->userMailer = $userMailer;
        $this->userService = $userService;
    }

    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
    }

    public function getDatatable()
    {
        return $this->userService->getDatatable(Auth::user()->account_id);
    }

    public function forcePDFJS()
    {
        $user = Auth::user();
        $user->force_pdfjs = true;
        $user->save();

        return Redirect::to('/dashboard')->with('success', trans('texts.updated_settings'));
    }

    public function create()
    {
        if (!Auth::user()->registered) {

            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('error', trans('texts.register_to_add_user'));
        }

        if (!Auth::user()->confirmed) {

            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('error', trans('texts.confirmation_required', ['link' => link_to('/resend_confirmation', trans('texts.click_here'))]));
        }

        if (Utils::isNinja() && !Auth::user()->canAddUsers()) {

            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('error', trans('texts.max_users_reached'));
        }

        $data = [
            'user' => null,
            'method' => 'POST',
            'url' => 'users',
        ];

        return View::make('users.edit', $data);
    }

    public function store(UserRequest $request)
    {
        $data = $request->input();

        if (!Auth::user()->hasFeature(FEATURE_USERS)) {
            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        $user = $this->userService->save($data);

        return redirect()->to("users/{$user->public_id}/edit")->with('success', trans('texts.created_user'));

    }

    public function edit($publicId)
    {
        $user = User::where('account_id', '=', Auth::user()->account_id)
            ->where('public_id', '=', $publicId)
            ->withTrashed()
            ->firstOrFail();

        $data = [
            'user' => $user,
            'method' => 'PUT',
            'url' => 'users/' . $publicId,
        ];

        return View::make('users.edit', $data);
    }

    public function update(UserRequest $request)
    {
        $data = $request->input();

        if (!Auth::user()->hasFeature(FEATURE_USERS)) {
            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        $user = $this->userService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {

            return redirect()->to(sprintf('users/%s/clone', $user->public_id))->with('success', trans('texts.clone_user'));
        } else {

            return redirect()->to("users/{$user->public_id}/edit")->with('success', trans('texts.updated_user'));
        }
    }

    public function show($publicId)
    {
        Session::reflash();

        return redirect("users/$publicId/edit");
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $id = Input::get('bulk_public_id');

        $user = User::where('account_id', '=', Auth::user()->account_id)
            ->where('public_id', '=', $id)->withTrashed()->firstOrFail();

        if ($action === 'archive') {
            $user->delete();
        } else {
            if (!Auth::user()->canAddUsers()) {
                return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('error', trans('texts.max_users_reached'));
            }

            $user->restore();
        }

        return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('message', trans("texts.{$action}d_user"));
    }

    public function save(UserRequest $request, $userPublicId = false)
    {
        $data = $request->input();

        if (!Auth::user()->hasFeature(FEATURE_USERS)) {
            return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }


//        if (!$user->confirmed && Input::get('action') === 'email') {
//            $this->userMailer->sendConfirmation($user, Auth::user());
//            $message = trans('texts.sent_invite');
//        } else {
//            $message = trans('texts.updated_user');
//        }

//        return Redirect::to('users/' . $user->public_id . '/edit')->with('success', $message);

    }

    public function sendConfirmation($userPublicId)
    {
        $user = User::where('account_id', '=', Auth::user()->account_id)
            ->where('public_id', '=', $userPublicId)->firstOrFail();

        $this->userMailer->sendConfirmation($user, Auth::user());

        return Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)->with('message', trans('texts.sent_invite'));
    }

    public function confirm($code)
    {
        $user = User::where('confirmation_code', '=', $code)->get()->first();

        if ($user) {
            $notice_msg = trans('texts.security_confirmation');
            $user->confirmed = true;
            $user->confirmation_code = null;
            $user->save();

            if ($user->public_id) {
                Auth::logout();
                Session::flush();
                $token = Password::getRepository()->create($user);

                return Redirect::to("/password/reset/{$token}");
            } else {
                if (Auth::check()) {
                    if (Session::has(REQUESTED_PRO_PLAN)) {
                        Session::forget(REQUESTED_PRO_PLAN);
                        $url = '/settings/account_management?upgrade=true';
                    } else {
                        $url = '/dashboard';
                    }
                } else {
                    $url = '/login';
                }

                return Redirect::to($url)->with('message', $notice_msg);
            }
        } else {
            $error_msg = trans('texts.wrong_confirmation');

            return Redirect::to('/login')->with('error', $error_msg);
        }
    }

    public function changePassword()
    {
        // check the current password is correct
        if (!Auth::validate([
            'email' => Auth::user()->email,
            'password' => Input::get('current_password'),
        ])) {
            return trans('texts.password_error_incorrect');
        }

        // validate the new password
        $password = Input::get('new_password');
        $confirm = Input::get('confirm_password');

        if (strlen($password) < 6 || $password != $confirm) {
            return trans('texts.password_error_invalid');
        }

        // save the new password
        $user = Auth::user();
        $user->password = bcrypt($password);
        $user->save();

        return RESULT_SUCCESS;
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

    public function viewAccountByKey($accountKey)
    {
        $user = $this->accountRepo->findUser(Auth::user(), $accountKey);

        if (!$user) {
            return redirect()->to('/');
        }

        Auth::loginUsingId($user->id);
        Auth::user()->account->loadLocalizationSettings();

        $redirectTo = request()->redirect_to ?: '/';

        return redirect()->to($redirectTo);
    }

    public function unlinkAccount($userAccountId, $userId)
    {
        $this->accountRepo->unlinkUser($userAccountId, $userId);
        $referer = Request::header('referer');

        $users = $this->accountRepo->loadAccounts(Auth::user()->id);
        Session::put(SESSION_USER_ACCOUNTS, $users);

        Session::flash('message', trans('texts.unlinked_account'));

        return Redirect::to('/manage_companies');
    }

    public function manageCompanies()
    {
        return View::make('users.account_management');
    }

    public function saveSidebarState()
    {
        if (Input::has('show_left')) {
            Session::put(SESSION_LEFT_SIDEBAR, boolval(Input::get('show_left')));
        }

        if (Input::has('show_right')) {
            Session::put(SESSION_RIGHT_SIDEBAR, boolval(Input::get('show_right')));
        }

        return RESULT_SUCCESS;
    }

    public function acceptTerms()
    {
        $ip = Request::getClientIp();
        $referer = Request::server('HTTP_REFERER');
        $message = '';

        if (request()->accepted_terms && request()->accepted_privacy) {
            auth()->user()->acceptLatestTerms($ip)->save();
            $message = trans('texts.accepted_terms');
        }

        return redirect($referer)->withMessage($message);
    }
}
