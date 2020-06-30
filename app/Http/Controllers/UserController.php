<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Libraries\Utils;
use App\Models\Branch;
use App\Models\Location;
use App\Models\PermissionGroup;
use App\Models\User;
use App\Ninja\Datatables\UserDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\UserRepository;
use App\Services\UserService;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Redirect;

class UserController extends BaseController
{
    private $userRepo;
    protected $accountRepo;
    protected $contactMailer;
    protected $userMailer;
    protected $userService;

    public function __construct(
        UserRepository $userRepo,
        AccountRepository $accountRepo,
        ContactMailer $contactMailer,
        UserMailer $userMailer,
        UserService $userService)
    {
        //parent::__construct();

        $this->userRepo = $userRepo;
        $this->accountRepo = $accountRepo;
        $this->contactMailer = $contactMailer;
        $this->userMailer = $userMailer;
        $this->userService = $userService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_USER);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_USER,
            'datatable' => new UserDatatable(),
            'title' => trans('texts.users'),
            'statuses' => User::getStatuses(),
        ]);
    }

    public function getDatatable($userPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->userService->getDatatable($accountId, $search);
    }

    public function getDatatableLocation($locationPublicId = null)
    {
        return $this->userService->getDatatableLocation($locationPublicId);
    }

    public function getDatatableBranch($branchPublicId = null)
    {
        return $this->userService->getDatatableBranch($branchPublicId);
    }

    public function forcePDFJS()
    {
        $user = Auth::user();
        $user->force_pdfjs = true;
        $user->save();

        Session::flash('success', trans('texts.updated_settings'));

        return Redirect::to('/dashboard');
    }

    public function create(UserRequest $request)
    {
        $this->authorize('create', ENTITY_USER);
        if ($request->location_id != 0) {
            $location = Location::scope($request->location_id)->firstOrFail();
        } else {
            $location = null;
        }
        if ($request->branch_id != 0) {
            $branch = Branch::scope($request->branch_id)->firstOrFail();
        } else {
            $branch = null;
        }

        $data = [
            'locationPublicId' => Input::old('location') ? Input::old('location') : $request->location_id,
            'branchPublicId' => Input::old('branch') ? Input::old('branch') : $request->branch_id,
            'user' => null,
            'userGroups' => null,
            'method' => 'POST',
            'url' => 'users',
            'title' => trans('texts.new_user'),
            'location' => $location,
            'branch' => $branch,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('users.edit', $data);
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->input();
        if (!Auth::user()->hasFeature(FEATURE_USERS)) {
            redirect()->to("users/")->with('error', trans('texts.error_created_user'));
        }

        $user = $this->userService->save($data);

        return redirect()->to("users/{$user->public_id}/edit")->with('success', trans('texts.created_user'));
    }

    public function edit(UserRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_USER);
        $user = $request->entity();
        if ($clone) {
            $user->id = null;
            $user->public_id = null;
            $user->deleted_at = null;
            $method = 'POST';
            $url = 'users';
        } else {
            $method = 'PUT';
            $url = 'users/' . $user->public_id;
        }
        $userGroups = $user->groups;

        $data = [
            'location' => null,
            'branch' => null,
            'user' => $user,
            'userGroups' => $userGroups,
            'entity' => $user,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_user'),
            'locationPublicId' => $user->location ? $user->location->public_id : null,
            'branchPublicId' => $user->branch ? $user->branch->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($user));

        return View::make('users.edit', $data);
    }

    public function update(UpdateUserRequest $request)
    {
        $data = $request->input();
        $user = $request->entity();
        $user = $this->userService->save($data, $user);

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            $message = trans('texts.clone_user');
            Session::flash('message', $message);
            return redirect()->to(sprintf('users/%s/clone', $user->public_id));
        } else {
            $message = trans('texts.updated_user');
            Session::flash('message', $message);
            return redirect()->to("users/{$user->public_id}/edit");
        }
    }

    public function show(UserRequest $request, $publicId)
    {
        $user = $request->entity();
        $account = Auth::user()->account;
        $accountId = $account->account_id;
        if ($user) {
            $actionLinks = [];
            if ($user->can('create', ENTITY_PERMISSION_GROUP)) {
                $actionLinks[] = ['label' => trans('texts.new_permission'), 'url' => URL::to('/permissions/create/' . $user->public_id)];
            }

            if ($user->can('create', ENTITY_PERMISSION_GROUP)) {
                $actionLinks[] = ['label' => trans('texts.new_groups'), 'url' => URL::to('/permission_groups/create/' . $user->public_id)];
            }

            if (!empty($actionLinks)) {
                $actionLinks[] = DropdownButton::DIVIDER;
            }
            $data = [
                'user' => $user,
                'account' => $account,
                'url' => null,
                'method' => null,
                'actionLinks' => $actionLinks,
                'showBreadcrumbs' => false,
                'title' => trans('texts.view_user'),
//                'hasPermissions' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && Permission::scope()->withArchived()->whereUserId($user->id)->count() > 0,
//                'hasGroups' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && PermissionGroup::scope()->withArchived()->whereUserId($user->id)->count() > 0,

            ];

            return View::make('users.show', $data);
        }
        return response()->view('errors/403');
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->userService->bulk($ids, $action);
        $message = Utils::pluralize($action . 'd_user', $count);

        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_USER, $action, $ids);
    }

    public function sendConfirmation($userPublicId)
    {
        $user = User::where('account_id', '=', Auth::user()->account_id)
            ->where('public_id', '=', $userPublicId)->firstOrFail();

        $this->userMailer->sendConfirmation($user, Auth::user());

        Session::flash('message', trans('texts.sent_invite'));

        return Redirect::to('users/');
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

                Session::flash('message', $notice_msg);

                return Redirect::to($url);
            }
        } else {
            $error_msg = trans('texts.wrong_confirmation');
            Session::flash('message', $error_msg);

            return Redirect::to('/login');
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
        $user = Auth::user();
        $user->password = bcrypt($password);

        if ($user->save()) {
            return RESULT_SUCCESS;
        }

        return RESULT_FAILURE;

    }

    public function changePermission()
    {
        $permissionArray = Input::get('permissions');
        $userAccount_id = Input::get('account_id');
        $userPublicId = Input::get('public_id');
        $userIsAdmin = Input::get('is_admin');
        $userIsAdmin = isset($userIsAdmin) ? boolval($userIsAdmin) : 0;

        $user = User::where('account_id', '=', $userAccount_id)->where('public_id', '=', $userPublicId)->firstOrFail();

        if ($user) {
            $user->permissions = $permissionArray;
            $user->is_admin = $userIsAdmin;

            $user->save();

            return response()->json(['success' => true, 'data' => RESULT_SUCCESS], 200);
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
                Session::put('_token', Str::random(40));
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

    public function cloneUser(UserRequest $request, $publicId)
    {
        if (Auth::user()->can('create', [ENTITY_USER])) {
            return self::edit($request, $publicId, true);
        }
        return false;
    }

    private static function getViewModel($user = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'locations' => Location::scope()->withActiveOrSelected($user ? $user->location_id : false)->orderBy('name')->get(),
            'branches' => Branch::scope()->withActiveOrSelected($user ? $user->branch_id : false)->orderBy('name')->get(),
            'groups' => PermissionGroup::scope()->withActiveOrSelected(false)->orderBy('name')->pluck('name', 'id'),
        ];
    }

}