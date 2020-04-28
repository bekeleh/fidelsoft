<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionGroupRequest;
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
    protected $permissionGroupRepo;
    protected $permissionGroupService;
    protected $entityType = ENTITY_PERMISSION_GROUP;

    public function __construct(PermissionGroupRepository $permissionGroupRepo, PermissionGroupService $permissionGroupService)
    {
        // parent::__construct();

        $this->permissionGroupRepo = $permissionGroupRepo;
        $this->permissionGroupService = $permissionGroupService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PERMISSION_GROUP,
            'datatable' => new PermissionGroupDatatable(),
            'title' => trans('texts.permission_groups'),
        ]);
    }

    public function getDatatable($permissionGroupPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->permissionGroupService->getDatatable($accountId, $search);
    }

    public function show($publicId)
    {
        $permissionGroup = PermissionGroup::where('public_id', $publicId)->firstOrFail();

//        $this->authorize('view', $permissionGroup);

        $user = Auth::user();
        $account = $user->account;
        $user->can('view', [ENTITY_PERMISSION_GROUP, $user]);

        $actionLinks = [];
        if ($user->can('create', ENTITY_PERMISSION_GROUP)) {
            $actionLinks[] = ['label' => trans('texts.new_permission_group'), 'url' => URL::to('/permission_groups/create/' . $permissionGroup->public_id)];
        }

        if (!empty($actionLinks)) {
            $actionLinks[] = \DropdownButton::DIVIDER;
        }
        $permissions = config('permissions');
        $permissionGroup->permissions = $permissionGroup->decodePermissions();
        $groupPermissions = Utils::selectedPermissionsArray($permissions, $permissionGroup->permissions);

        $data = [
            'groupPermissions' => $groupPermissions,
            'permissions' => $permissions,
            'permissionGroup' => $permissionGroup,
            'account' => $account,
            'actionLinks' => $actionLinks,
            'showBreadcrumbs' => false,
            'title' => trans('texts.view_permission_group'),
//            'hasPermissions' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && Permission::scope()->withArchived()->whereUserId($permissionGroup->id)->count() > 0,
//            'hasGroups' => $account->isModuleEnabled(ENTITY_PERMISSION_GROUP) && PermissionGroup::scope()->withArchived()->whereUserId($permissionGroup->id)->count() > 0,

        ];

        return View::make('permission_groups.show', $data);
    }

    public function create(PermissionGroupRequest $request)
    {
        $data = [
            'permissionGroup' => null,
            'method' => 'POST',
            'url' => 'permission_groups',
            'title' => trans('texts.new_permission_group'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('permission_groups.edit', $data);
    }

    public function edit(PermissionGroupRequest $request, $publicId = false, $clone = false)
    {
        $permissionGroup = $request->entity();
        if ($clone) {
            $permissionGroup->id = null;
            $permissionGroup->public_id = null;
            $permissionGroup->deleted_at = null;
            $method = 'POST';
            $url = 'permission_groups';
        } else {
            $method = 'PUT';
            $url = 'permission_groups/' . $permissionGroup->public_id;
        }

        $data = [
            'permissionGroup' => $permissionGroup,
            'entity' => $permissionGroup,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_permission_group'),
        ];

        $data = array_merge($data, self::getViewModel($permissionGroup));

        return View::make('permission_groups.edit', $data);
    }

    public function update(PermissionGroupRequest $request)
    {
        $data = $request->input();

        $permissionGroup = $this->permissionGroupService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('permission_groups/%s/clone', $permissionGroup->public_id))->with('message', trans('texts.clone_permission_group'));
        } else {
            return redirect()->to("permission_groups/{$permissionGroup->public_id}/edit")->with('message', trans('texts.updated_permission_group'));
        }
    }

    public function store(PermissionGroupRequest $request)
    {
        $data = $request->input();

        $permissionGroup = $this->permissionGroupService->save($data);

        return redirect()->to("permission_groups/{$permissionGroup->public_id}/edit")->with('message', trans('texts.created_permission_group'));
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
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($permissionGroup = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }

    public function changePermission()
    {
        $permissionArray = Input::get('permission');
        $permissionGroupAccountId = Input::get('account_id');
        $permissionGroupPublicId = Input::get('public_id');
        $permissionGroup = PermissionGroup::where('account_id', '=', $permissionGroupAccountId)
            ->where('public_id', '=', $permissionGroupPublicId)->firstOrFail();
        if ($permissionGroup) {
            $permissionGroup->permissions = $permissionArray;
            $permissionGroup->save();

            return response()->json(['success' => true, 'data' => $permissionGroup], 200);
        }

        return response()->json(['error' => true, 'data' => ''], 200);
    }
}
