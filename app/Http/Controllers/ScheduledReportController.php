<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatescheduledReportRequest;
use App\Http\Requests\scheduledReportRequest;
use App\Http\Requests\UpdatescheduledReportRequest;
use App\Ninja\Datatables\scheduledReportDatatable;
use App\Ninja\Repositories\scheduledReportRepository;
use App\Services\scheduledReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class scheduledReportController extends BaseController
{
    protected $scheduledReportRepo;
    protected $scheduledReportService;
    protected $entityType = ENTITY_SCHEDULED_REPORT;

    public function __construct(scheduledReportRepository $scheduledReportRepo, scheduledReportService $scheduledReportService)
    {
        $this->scheduledReportRepo = $scheduledReportRepo;
        $this->scheduledReportService = $scheduledReportService;
    }

    public function index()
    {
        $this->authorize('index', auth::user(), $this->entityType);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_SCHEDULED_REPORT,
            'datatable' => new scheduledReportDatatable(),
            'title' => trans('texts.scheduled_reports'),
        ]);
    }

    public function getDatatable($scheduledReportPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->scheduledReportService->getDatatable($accountId, $search);
    }

    public function create(scheduledReportRequest $request)
    {
        $this->authorize('create', auth::user(), $this->entityType);
        $data = [
            'scheduledReport' => null,
            'method' => 'POST',
            'url' => 'scheduled_reports',
            'title' => trans('texts.new_scheduled_report'),
        ];

        return View::make('scheduled_reports.edit', $data);
    }

    public function store(CreatescheduledReportRequest $request)
    {
        $scheduledReport = $this->scheduledReportRepo->save($request->input());

        Session::flash('message', trans('texts.created_scheduled_report'));

        return redirect()->to($scheduledReport->getRoute());
    }

    public function edit(scheduledReportRequest $request)
    {
        $this->authorize('edit', auth::user(), $this->entityType);
        $scheduledReport = $request->entity();

        $data = [
            'scheduledReport' => $scheduledReport,
            'method' => 'PUT',
            'url' => 'scheduled_reports/' . $scheduledReport->public_id,
            'title' => trans('texts.edit_scheduled_report'),
        ];

        return View::make('scheduled_reports.edit', $data);
    }

    public function update(UpdatescheduledReportRequest $request)
    {
        $scheduledReport = $this->scheduledReportRepo->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_scheduled_report'));

        return redirect()->to($scheduledReport->getRoute());
    }

    public function cloneScheduledReport(scheduledReportRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->scheduledReportService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_scheduled_report" : "{$action}d_scheduled_reports";
            $message = trans("texts.$field", ['count' => $count]);
            Session::flash('message', $message);
        }

        return redirect()->to('/scheduled_reports');
    }
}
