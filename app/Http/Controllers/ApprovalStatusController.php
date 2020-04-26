<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Libraries\Utils;
use App\Models\Status;
use App\Ninja\Datatables\StatusDatatable;
use App\Ninja\Repositories\StatusRepository;
use App\Services\StatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class StatusController.
 */
class StatusController extends BaseController
{
    protected $StatusService;

    protected $StatusRepo;

    /**
     * StatusController constructor.
     *
     * @param StatusService $StatusService
     * @param StatusRepository $StatusRepo
     */
    public function __construct(StatusService $StatusService, StatusRepository $StatusRepo)
    {
        //parent::__construct();

        $this->StatusService = $StatusService;
        $this->StatusRepo = $StatusRepo;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_APPROVAL_STATUS,
            'datatable' => new StatusDatatable(),
            'title' => trans('texts.approval_statuses'),
            'statuses' => Status::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("approval_statuses/$publicId/edit");
    }

    public function getDatatable()
    {
        return $this->StatusService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function edit(StatusRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_APPROVAL_STATUS, $request->entity()]);

        $account = Auth::user()->account;

        $Status = Status::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $Status->id = null;
            $Status->public_id = null;
            $Status->deleted_at = null;
            $url = 'approval_statuses';
            $method = 'POST';
        } else {
            $url = 'approval_statuses/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'Status' => $Status,
            'entity' => $Status,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_approval_status'),
        ];

        return View::make('approval_statuses.edit', $data);
    }

    public function create(StatusRequest $request)
    {

        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'Status' => null,
            'method' => 'POST',
            'url' => 'approval_statuses',
            'title' => trans('texts.create_approval_status'),
        ];

        return View::make('approval_statuses.edit', $data);
    }

    public function store(StatusRequest $request)
    {
        return $this->save();
    }

    public function update(StatusRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    private function save($StatusPublicId = false)
    {
        if ($StatusPublicId) {
            $Status = Status::scope($StatusPublicId)->withTrashed()->firstOrFail();
        } else {
            $Status = Status::createNew();
        }
        $this->StatusRepo->save(Input::all(), $Status);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'relocation'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('approval_statuses/%s/clone', $Status->public_id))->with('success', trans('texts.clone_approval_status'));
        } else {
            return redirect()->to("approval_statuses/{$Status->public_id}/edit")->with('success', trans('texts.updated_approval_status'));
        }
    }

    public function cloneStatus(StatusRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->StatusService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_approval_status', $count);

        return $this->returnBulk(ENTITY_APPROVAL_STATUS, $action, $ids)->with('message', $message);
    }
}
