<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalStatusRequest;
use App\Libraries\Utils;
use App\Models\ApprovalStatus;
use App\Ninja\Datatables\ApprovalStatusDatatable;
use App\Ninja\Repositories\ApprovalStatusRepository;
use App\Services\ApprovalStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ApprovalStatusController.
 */
class ApprovalStatusController extends BaseController
{
    protected $ApprovalStatusService;

    protected $approvalStatusRepo;

    /**
     * ApprovalStatusController constructor.
     *
     * @param ApprovalStatusService $ApprovalStatusService
     * @param ApprovalStatusRepository $approvalStatusRepo
     */
    public function __construct(ApprovalStatusService $ApprovalStatusService, ApprovalStatusRepository $approvalStatusRepo)
    {
        //parent::__construct();

        $this->ApprovalStatusService = $ApprovalStatusService;
        $this->approvalStatusRepo = $approvalStatusRepo;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_APPROVAL_STATUS,
            'datatable' => new ApprovalStatusDatatable(),
            'title' => trans('texts.approval_statuses'),
            'statuses' => ApprovalStatus::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("approval_statuses/$publicId/edit");
    }

    public function getDatatable()
    {
        return $this->approvalStatusService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function edit(ApprovalStatusRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_APPROVAL_STATUS, $request->entity()]);

        $account = Auth::user()->account;

        $approvalStatus = ApprovalStatus::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $approvalStatus->id = null;
            $approvalStatus->public_id = null;
            $approvalStatus->deleted_at = null;
            $url = 'approval_statuses';
            $method = 'POST';
        } else {
            $url = 'approval_statuses/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'itemCategory' => $approvalStatus,
            'entity' => $approvalStatus,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_approval_status'),
        ];

        return View::make('approval_statuses.edit', $data);
    }

    public function create(ApprovalStatusRequest $request)
    {

        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'itemCategory' => null,
            'method' => 'POST',
            'url' => 'approval_statuses',
            'title' => trans('texts.create_approval_status'),
        ];

        return View::make('approval_statuses.edit', $data);
    }

    public function store(ApprovalStatusRequest $request)
    {
        return $this->save();
    }

    public function update(ApprovalStatusRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    private function save($approvalStatusPublicId = false)
    {
        if ($approvalStatusPublicId) {
            $approvalStatus = ApprovalStatus::scope($approvalStatusPublicId)->withTrashed()->firstOrFail();
        } else {
            $approvalStatus = ApprovalStatus::createNew();
        }
        $this->approvalStatusRepo->save(Input::all(), $approvalStatus);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'relocation'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('approval_statuses/%s/clone', $approvalStatus->public_id))->with('success', trans('texts.clone_approval_status'));
        } else {
            return redirect()->to("approval_statuses/{$approvalStatus->public_id}/edit")->with('success', trans('texts.updated_approval_status'));
        }
    }

    public function cloneApprovalStatus(ApprovalStatusRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->approvalStatusService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_approval_status', $count);

        return $this->returnBulk(ENTITY_APPROVAL_STATUS, $action, $ids)->with('message', $message);
    }
}
