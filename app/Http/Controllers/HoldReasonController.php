<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHoldReasonRequest;
use App\Http\Requests\HoldReasonRequest;
use App\Http\Requests\UpdateHoldReasonRequest;
use App\Libraries\Utils;
use App\Models\HoldReason;
use App\Ninja\Datatables\HoldReasonDatatable;
use App\Ninja\Repositories\HoldReasonRepository;
use App\Services\HoldReasonService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class HoldReasonController.
 */
class HoldReasonController extends BaseController
{
    /**
     * @var HoldReasonService
     */
    protected $holdReasonService;

    /**
     * @var HoldReasonRepository
     */
    protected $holdReasonRepo;

    /**
     * HoldReasonController constructor.
     *
     * @param HoldReasonService $holdReasonService
     * @param HoldReasonRepository $holdReasonRepo
     */
    public function __construct(HoldReasonService $holdReasonService, HoldReasonRepository $holdReasonRepo)
    {
        //parent::__construct();
        $this->holdReasonService = $holdReasonService;
        $this->holdReasonRepo = $holdReasonRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_HOLD_REASON);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_HOLD_REASON,
            'datatable' => new HoldReasonDatatable(),
            'title' => trans('texts.hold_reasons'),
            'statuses' => HoldReason::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("hold_reasons/{$publicId}/edit");
    }

    public function getDatatable()
    {
        return $this->holdReasonService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(HoldReasonRequest $request)
    {
        $this->authorize('create', ENTITY_HOLD_REASON);
        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'holdReason' => null,
            'method' => 'POST',
            'url' => 'hold_reasons',
            'title' => trans('texts.create_hold_reason'),
        ];

        return View::make('hold_reasons.edit', $data);
    }

    public function store(CreateHoldReasonRequest $request)
    {

        return $this->save();
    }

    public function edit(HoldReasonRequest $request, $publicId, $clone = false)
    {
        $this->authorize('edit', ENTITY_HOLD_REASON);
        $account = Auth::user()->account;

        $holdReason = HoldReason::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $holdReason->id = null;
            $holdReason->public_id = null;
            $holdReason->deleted_at = null;
            $url = 'hold_reasons';
            $method = 'POST';
        } else {
            $url = 'hold_reasons/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'holdReason' => $holdReason,
            'entity' => $holdReason,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_hold_reason'),
        ];

        return View::make('hold_reasons.edit', $data);
    }

    public function update(UpdateHoldReasonRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    private function save($holdReasonPublicId = false)
    {
        if ($holdReasonPublicId) {
            $holdReason = HoldReason::scope($holdReasonPublicId)->withTrashed()->firstOrFail();
        } else {
            $holdReason = HoldReason::createNew();
        }
        $this->holdReasonRepo->save(Input::all(), $holdReason);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'relocation', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('hold_reasons/%s/clone', $holdReason->public_id))->with('success', trans('texts.clone_hold_reason'));
        } else {
            return redirect()->to("hold_reasons/{$holdReason->public_id}/edit")->with('success', trans('texts.updated_hold_reason'));
        }
    }

    public function cloneHoldReason(HoldReasonRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->holdReasonService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_hold_reasons', $count);

        return $this->returnBulk(ENTITY_HOLD_REASON, $action, $ids)->with('message', $message);
    }
}
