<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Libraries\Utils;
use App\Models\Department;
use App\Ninja\Datatables\DepartmentDatatable;
use App\Ninja\Repositories\DepartmentRepository;
use App\Services\DepartmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class DepartmentController.
 */
class DepartmentController extends BaseController
{

    protected $departmentService;

    protected $departmentRepo;

    /**
     * DepartmentController constructor.
     *
     * @param DepartmentService $departmentService
     * @param DepartmentRepository $departmentRepo
     */
    public function __construct(DepartmentService $departmentService, DepartmentRepository $departmentRepo)
    {
        //parent::__construct();
        $this->departmentService = $departmentService;
        $this->departmentRepo = $departmentRepo;
    }

    public function index()
    {
        $this->authorize('view', auth::user(), $this->entityType);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_DEPARTMENT,
            'datatable' => new DepartmentDatatable(),
            'title' => trans('texts.departments'),
            'statuses' => Department::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("departments/$publicId/edit");
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->departmentService->getDatatable($accountId, $search);
    }

    public function create(DepartmentRequest $request)
    {
        Auth::user()->can('create', [ENTITY_DEPARTMENT, $request->entity()]);
        $data = [
            'department' => null,
            'method' => 'POST',
            'url' => 'departments',
            'title' => trans('texts.create_department'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('departments.edit', $data);
    }

    public function store(CreateDepartmentRequest $request)
    {
        $data = $request->input();

        $department = $this->departmentService->save($data);

        return redirect()->to("departments/{$department->public_id}/edit")->with('success', trans('texts.created_department'));
    }

    public function edit(DepartmentRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('edit', [ENTITY_DEPARTMENT, $request->entity()]);

        $department = Department::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $department->id = null;
            $department->public_id = null;
            $department->deleted_at = null;
            $method = 'POST';
            $url = 'departments';
        } else {
            $method = 'PUT';
            $url = 'departments/' . $department->public_id;
        }

        $data = [
            'department' => $department,
            'entity' => $department,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_department'),
        ];

        $data = array_merge($data, self::getViewModel($department));

        return View::make('departments.edit', $data);
    }

    public function update(UpdateDepartmentRequest $request, $publicId)
    {
        $data = $request->input();
        $department = $this->departmentService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('departments/%s/clone', $department->public_id))->with('success', trans('texts.clone_department'));
        } else {
            return redirect()->to("departments/{$department->public_id}/edit")->with('success', trans('texts.updated_department'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $departments = Department::scope($ids)->get();
            $data = [];
            foreach ($departments as $department) {
                $data[] = $department->department_key;
            }
            return redirect("invoices/create")->with('departments', $data);
        } else {
            $count = $this->departmentService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_department', $count);

        return $this->returnBulk(ENTITY_DEPARTMENT, $action, $ids)->with('success', $message);
    }

    public function cloneDepartment(DepartmentRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($department = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
