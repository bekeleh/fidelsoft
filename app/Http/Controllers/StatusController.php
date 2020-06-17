<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStatusRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\UpdateStatusRequest;
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
        $this->authorize('view', auth::user(), $this->entityType);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_STATUS,
            'datatable' => new StatusDatatable(),
            'title' => trans('texts.statuses'),
            'statuses' => Status::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("statuses/$publicId/edit");
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->StatusService->getDatatable($accountId, $search);
    }

    public function edit(StatusRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_STATUS, $request->entity()]);

        $account = Auth::user()->account;

        $Status = Status::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $Status->id = null;
            $Status->public_id = null;
            $Status->deleted_at = null;
            $url = 'statuses';
            $method = 'POST';
        } else {
            $url = 'statuses/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'Status' => $Status,
            'entity' => $Status,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_status'),
        ];

        return View::make('statuses.edit', $data);
    }

    public function create(StatusRequest $request)
    {

        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'Status' => null,
            'method' => 'POST',
            'url' => 'statuses',
            'title' => trans('texts.create_status'),
        ];

        return View::make('statuses.edit', $data);
    }

    public function store(CreateStatusRequest $request)
    {
        return $this->save();
    }

    public function update(UpdateStatusRequest $request, $publicId)
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
            return redirect()->to(sprintf('statuses/%s/clone', $Status->public_id))->with('success', trans('texts.clone_status'));
        } else {
            return redirect()->to("statuses/{$Status->public_id}/edit")->with('success', trans('texts.updated_status'));
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

        $message = Utils::pluralize($action . 'd_status', $count);

        return $this->returnBulk(ENTITY_STATUS, $action, $ids)->with('message', $message);
    }
}
