<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionGroupRequest;
use App\Http\Requests\PermissionGroupRequest;
use App\Http\Requests\UpdatePermissionGroupRequest;
use App\Libraries\Utils;
use App\Models\PermissionGroup;
use App\Ninja\Datatables\PermissionGroupDatatable;
use App\Ninja\Repositories\PermissionGroupRepository;
use App\Services\PermissionGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class PermissionGroupController extends BaseController
{
    protected $userGroupRepo;
    protected $userGroupService;
    protected $entityType = ENTITY_PERMISSION_GROUP;

    protected $permissionGroupRepo;
    protected $permissionGroupService;

    public function __construct(PermissionGroupRepository $userGroupRepo, PermissionGroupService $userGroupService)
    {
        // parent::__construct();

        $this->permissionGroupRepo = $userGroupRepo;
        $this->permissionGroupService = $userGroupService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PERMISSION_GROUP,
            'datatable' => new PermissionGroupDatatable(),
            'title' => trans('texts.permission_groups'),
        ]);
    }

    public function getDatatable($userGroupPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->permissionGroupService->getDatatable($accountId, $search);
    }

    public function show($publicId)
    {
        $userGroup = PermissionGroup::where('public_id', $publicId)->firstOrFail();

        $user = Auth::user();
        $account = $user->account;
        $user->can('view', [ENTITY_PERMISSION_GROUP, $user]);

        $actionLinks = [];
        if ($user->can('create', ENTITY_PERMISSION_GROUP)) {
            $actionLinks[] = ['label' => trans('texts.new_permission_group'), 'url' => URL::to('/permission_groups/create/' . $userGroup->public_id)];
        }

        if (!empty($actionLinks)) {
            $actionLinks[] = \DropdownButton::DIVIDER;
        }

        $data = [
            'userGroup' => $userGroup,
            'account' => $account,
            'actionLinks' => $actionLinks,
            'showBreadcrumbs' => false,
            'title' => trans('texts.view_permission_group'),
//            'hasPermissions' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && Permission::scope()->withArchived()->whereUserId($userGroup->id)->count() > 0,
//            'hasGroups' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && PermissionGroup::scope()->withArchived()->whereUserId($userGroup->id)->count() > 0,

        ];

        return View::make('permission_groups.show', $data);
    }

    public function create(PermissionGroupRequest $request)
    {
        $data = [
            'userGroup' => null,
            'method' => 'POST',
            'url' => 'permission_groups',
            'title' => trans('texts.new_permission_group'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('permission_groups.edit', $data);
    }

    public function store(CreatePermissionGroupRequest $request)
    {
        $data = $request->input();

        $userGroup = $this->permissionGroupService->save($data);

        return redirect()->to("permission_groups/{$userGroup->public_id}/edit")->with('message', trans('texts.created_permission_group'));
    }

    public function edit(PermissionGroupRequest $request, $publicId = false, $clone = false)
    {
        $userGroup = $request->entity();
        if ($clone) {
            $userGroup->id = null;
            $userGroup->public_id = null;
            $userGroup->deleted_at = null;
            $method = 'POST';
            $url = 'permission_groups';
        } else {
            $method = 'PUT';
            $url = 'permission_groups/' . $userGroup->public_id;
        }

        $data = [
            'userGroup' => $userGroup,
            'entity' => $userGroup,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_permission_group'),
        ];

        $data = array_merge($data, self::getViewModel($userGroup));

        return View::make('permission_groups.edit', $data);
    }

    public function update(UpdatePermissionGroupRequest $request)
    {
        $data = $request->input();

        $userGroup = $this->permissionGroupService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('permission_groups/%s/clone', $userGroup->public_id))->with('message', trans('texts.clone_permission_group'));
        } else {
            return redirect()->to("permission_groups/{$userGroup->public_id}/edit")->with('message', trans('texts.updated_permission_group'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');

        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->permissionGroupService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_permission_group', $count);

        return $this->returnBulk(ENTITY_PERMISSION_GROUP, $action, $ids)->with('message', $message);
    }

    public function clonePermissionGroup(PermissionGroupRequest $request, $publicId)
    {
        if (Auth::user()->can('create', [ENTITY_PERMISSION_GROUP])) {
            return self::edit($request, $publicId, true);
        }
    }

    private static function getViewModel($userGroup = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }

    public function changePermission()
    {
        $permissionArray = Input::get('permissions');
        $userGroupAccountId = Input::get('account_id');
        $userGroupPublicId = Input::get('public_id');
        $userGroup = PermissionGroup::where('account_id', '=', $userGroupAccountId)
            ->where('public_id', '=', $userGroupPublicId)->firstOrFail();
        if ($userGroup) {
            $userGroup->permissions = $permissionArray;
            $userGroup->save();

            return response()->json(['success' => true, 'data' => RESULT_SUCCESS], 200);
        }

        return response()->json(['error' => true, 'data' => RESULT_FAILURE], 200);
    }
}
