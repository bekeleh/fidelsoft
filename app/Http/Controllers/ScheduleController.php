<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Ninja\Datatables\ScheduleDatatable;
use App\Ninja\Repositories\ScheduleRepository;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class scheduleController extends BaseController
{
    protected $scheduleRepo;
    protected $scheduleService;
    protected $entityType = ENTITY_SCHEDULE;

    public function __construct(ScheduleRepository $scheduleRepo, ScheduleService $scheduleService)
    {
        $this->scheduleRepo = $scheduleRepo;
        $this->scheduleService = $scheduleService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_SCHEDULE);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_SCHEDULE,
            'datatable' => new ScheduleDatatable(),
            'title' => trans('texts.schedules'),
        ]);
    }

    public function getDatatable($schedulePublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->scheduleService->getDatatable($accountId, $search);
    }

    public function create(ScheduleRequest $request)
    {
        $this->authorize('create', ENTITY_SCHEDULE);
        $data = [
            'schedule' => null,
            'method' => 'POST',
            'url' => 'schedules',
            'title' => trans('texts.new_schedule'),
        ];

        return View::make('schedules.edit', $data);
    }

    public function edit(ScheduleRequest $request)
    {
        $this->authorize('edit', ENTITY_SCHEDULE);
        $schedule = $request->entity();

        $data = [
            'schedule' => $schedule,
            'method' => 'PUT',
            'url' => 'schedules/' . $schedule->public_id,
            'title' => trans('texts.edit_schedule'),
        ];

        return View::make('schedules.edit', $data);
    }

    public function store(CreateScheduleRequest $request)
    {
        $schedule = $this->scheduleRepo->save($request->input());

        Session::flash('message', trans('texts.created_schedule'));

        return redirect()->to($schedule->getRoute());
    }

    public function update(UpdateScheduleRequest $request)
    {
        $schedule = $this->scheduleRepo->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_schedule'));

        return redirect()->to($schedule->getRoute());
    }

    public function cloneSchedule(ScheduleRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->scheduleService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_schedule" : "{$action}d_schedules";
            $message = trans("texts.$field", ['count' => $count]);
            Session::flash('message', $message);
        }

        return redirect()->to('/schedules');
    }
}
