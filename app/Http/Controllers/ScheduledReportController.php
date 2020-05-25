<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduledReportRequest;
use App\Http\Requests\ScheduledReportRequest;
use App\Http\Requests\UpdateScheduledReportRequest;
use App\Ninja\Datatables\ScheduledReportDatatable;
use App\Ninja\Repositories\ScheduledReportRepository;
use App\Services\ScheduledReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ScheduledReportController extends BaseController
{
    protected $ScheduledReportRepo;
    protected $ScheduledReportService;
    protected $entityType = ENTITY_SCHEDULED_REPORT;

    public function __construct(ScheduledReportRepository $ScheduledReportRepo, ScheduledReportService $ScheduledReportService)
    {
        $this->ScheduledReportRepo = $ScheduledReportRepo;
        $this->ScheduledReportService = $ScheduledReportService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_SCHEDULED_REPORT,
            'datatable' => new ScheduledReportDatatable(),
            'title' => trans('texts.scheduled_reports'),
        ]);
    }

    public function getDatatable($ScheduledReportPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->ScheduledReportService->getDatatable($accountId, $search);
    }

    public function create(ScheduledReportRequest $request)
    {
        $data = [
            'ScheduledReport' => null,
            'method' => 'POST',
            'url' => 'scheduled_reports',
            'title' => trans('texts.new_scheduled_report'),
        ];

        return View::make('scheduled_reports.edit', $data);
    }

    public function edit(ScheduledReportRequest $request)
    {
        $ScheduledReport = $request->entity();

        $data = [
            'ScheduledReport' => $ScheduledReport,
            'method' => 'PUT',
            'url' => 'scheduled_reports/' . $ScheduledReport->public_id,
            'title' => trans('texts.edit_scheduled_report'),
        ];

        return View::make('scheduled_reports.edit', $data);
    }

    public function store(CreateScheduledReportRequest $request)
    {
        $ScheduledReport = $this->ScheduledReportRepo->save($request->input());

        Session::flash('message', trans('texts.created_scheduled_report'));

        return redirect()->to($ScheduledReport->getRoute());
    }

    public function update(UpdateScheduledReportRequest $request)
    {
        $ScheduledReport = $this->ScheduledReportRepo->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_scheduled_report'));

        return redirect()->to($ScheduledReport->getRoute());
    }

    public function cloneScheduledReport(ScheduledReportRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->ScheduledReportService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_scheduled_report" : "{$action}d_scheduled_reports";
            $message = trans("texts.$field", ['count' => $count]);
            Session::flash('message', $message);
        }

        return redirect()->to('/scheduled_reports');
    }
}
