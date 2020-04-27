<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Libraries\Utils;
use App\Models\Group;
use App\Models\Permission;
use App\Ninja\Datatables\GroupDatatable;
use App\Ninja\Repositories\GroupRepository;
use App\Services\GroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class GroupController extends BaseController
{
    protected $groupRepo;
    protected $groupService;
    protected $entityType = ENTITY_GROUP;

    public function __construct(GroupRepository $groupRepo, GroupService $groupService)
    {
        // parent::__construct();

        $this->groupRepo = $groupRepo;
        $this->groupService = $groupService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_GROUP,
            'datatable' => new GroupDatatable(),
            'title' => trans('texts.groups'),
        ]);
    }

    public function show($publicId)
    {
        $group = new Group();

        $user = Auth::user();
        $account = $user->account;
        $group->can('view', [ENTITY_GROUP, $user]);

        $actionLinks = [];
        if ($group->can('create', ENTITY_GROUP)) {
            $actionLinks[] = ['label' => trans('texts.new_permission'), 'url' => URL::to('/groups/create/' . $group->public_id)];
        }

        if ($group->can('create', ENTITY_GROUP)) {
            $actionLinks[] = ['label' => trans('texts.new_groups'), 'url' => URL::to('/groups/create/' . $group->public_id)];
        }

        if (!empty($actionLinks)) {
            $actionLinks[] = \DropdownButton::DIVIDER;
        }
        $permissions = config('permissions');
        $group->permissions = $this->groupService->decodePermissions();
        $groupPermissions = Utils::selectedPermissionsArray($permissions, $group->permissions);

        $data = [
            'group' => 'detail',
            'permissions' => $permissions,
            'groupPermissions' => $groupPermissions,
            'account' => $account,
            'actionLinks' => $actionLinks,
            'showBreadcrumbs' => false,
            'title' => trans('texts.view_user'),
            'hasPermissions' => $account->isModuleEnabled(ENTITY_PERMISSION) && Permission::scope()->withArchived()->whereUserId($group->id)->count() > 0,
            'hasGroups' => $account->isModuleEnabled(ENTITY_GROUP) && Group::scope()->withArchived()->whereUserId($group->id)->count() > 0,
        ];

        return View::make('users.show', $data);
    }

    public function getDatatable($groupPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->groupService->getDatatable($accountId, $search);
    }

    public function create(GroupRequest $request)
    {
        $data = [
            'group' => null,
            'method' => 'POST',
            'url' => 'groups',
            'title' => trans('texts.new_group'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('groups.edit', $data);
    }

    public function edit(GroupRequest $request, $publicId = false, $clone = false)
    {
        $group = $request->entity();
        if ($clone) {
            $group->id = null;
            $group->public_id = null;
            $group->deleted_at = null;
            $method = 'POST';
            $url = 'groups';
        } else {
            $method = 'PUT';
            $url = 'groups/' . $group->public_id;
        }

        $data = [
            'group' => $group,
            'entity' => $group,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.group.edit'),
        ];

        $data = array_merge($data, self::getViewModel($group));

        return View::make('groups.edit', $data);
    }

    public function update(GroupRequest $request)
    {
        $data = $request->input();

        $group = $this->groupService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('groups/%s/clone', $group->public_id))->with('message', trans('texts.clone_group'));
        } else {
            return redirect()->to("groups/{$group->public_id}/edit")->with('message', trans('texts.updated_group'));
        }
    }

    public function store(GroupRequest $request)
    {
        $data = $request->input();

        $group = $this->groupService->save($data);

        return redirect()->to("groups/{$group->public_id}/edit")->with('message', trans('texts.created_group'));
    }

    public function bulk()
    {
        $action = Input::get('action');

        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->groupService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_group', $count);

        return $this->returnBulk(ENTITY_GROUP, $action, $ids)->with('message', $message);
    }

    public function cloneGroup(GroupRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($group = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
