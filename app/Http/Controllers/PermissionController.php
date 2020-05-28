<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Libraries\Utils;
use App\Ninja\Datatables\PermissionDatatable;
use App\Ninja\Repositories\PermissionGroupRepository;
use App\Services\PermissionGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class PermissionController extends BaseController
{
    protected $permissionRepo;
    protected $permissionService;
    protected $entityType = ENTITY_PERMISSION;

    public function __construct(PermissionGroupRepository $permissionRepo, PermissionGroupService $permissionService)
    {
        // parent::__construct();

        $this->permissionRepo = $permissionRepo;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PERMISSION,
            'datatable' => new PermissionDatatable(),
            'title' => trans('texts.permissions'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("permissions/{$publicId}/edit");
    }

    public function getDatatable($permissionPublicId = null)
    {
        return $this->permissionService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(PermissionRequest $request)
    {
        $data = [
            'group' => null,
            'method' => 'POST',
            'url' => 'permissions',
            'title' => trans('texts.new_permission'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('permissions.edit', $data);
    }

    public function edit(PermissionRequest $request, $publicId = false, $clone = false)
    {
        $permission = $request->entity();
        if ($clone) {
            $permission->id = null;
            $permission->public_id = null;
            $permission->deleted_at = null;
            $method = 'POST';
            $url = 'permissions';
        } else {
            $method = 'PUT';
            $url = 'permissions/' . $permission->public_id;
        }

        $data = [
            'group' => $permission,
            'entity' => $permission,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.group.edit'),
        ];

        $data = array_merge($data, self::getViewModel($permission));

        return View::make('permissions.edit', $data);
    }

    public function store(CreatePermissionRequest $request)
    {
        $data = $request->input();

        $permission = $this->permissionService->save($data);

        return redirect()->to("permissions/{$permission->public_id}/edit")->with('message', trans('texts.created_permission'));
    }

    public function update(UpdatePermissionRequest $request)
    {
        $data = $request->input();

        $permission = $this->permissionService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('permissions/%s/clone', $permission->public_id))->with('message', trans('texts.clone_permission'));
        } else {
            return redirect()->to("permissions/{$permission->public_id}/edit")->with('message', trans('texts.updated_permission'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');

        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->permissionService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_permission', $count);

        return $this->returnBulk(ENTITY_PERMISSION, $action, $ids)->with('message', $message);
    }

    public function clonePermission(PermissionRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($permission = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
