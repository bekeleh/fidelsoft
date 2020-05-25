<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleCategoryRequest;
use App\Http\Requests\ScheduleCategoryRequest;
use App\Http\Requests\UpdateScheduleCategoryRequest;
use App\Ninja\Datatables\ScheduleCategoryDatatable;
use App\Ninja\Repositories\ScheduleCategoryRepository;
use App\Services\ScheduleCategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ScheduleCategoryController extends BaseController
{
    protected $scheduleCategoryRepo;
    protected $scheduleCategoryService;
    protected $entityType = ENTITY_SCHEDULE_CATEGORY;

    public function __construct(ScheduleCategoryRepository $scheduleCategoryRepo, ScheduleCategoryService $scheduleCategoryService)
    {
        $this->scheduleCategoryRepo = $scheduleCategoryRepo;
        $this->scheduleCategoryService = $scheduleCategoryService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_SCHEDULE_CATEGORY,
            'datatable' => new ScheduleCategoryDatatable(),
            'title' => trans('texts.schedule_categories'),
        ]);
    }

    public function getDatatable($scheduleCategoryPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->scheduleCategoryService->getDatatable($accountId, $search);
    }

    public function create(ScheduleCategoryRequest $request)
    {
        $data = [
            'scheduleCategory' => null,
            'method' => 'POST',
            'url' => 'schedule_categories',
            'title' => trans('texts.new_schedule_category'),
        ];

        return View::make('schedule_categories.edit', $data);
    }

    public function edit(ScheduleCategoryRequest $request)
    {
        $scheduleCategory = $request->entity();

        $data = [
            'scheduleCategory' => $scheduleCategory,
            'method' => 'PUT',
            'url' => 'schedule_categories/' . $scheduleCategory->public_id,
            'title' => trans('texts.edit_category'),
        ];

        return View::make('schedule_categories.edit', $data);
    }

    public function store(CreateScheduleCategoryRequest $request)
    {
        $scheduleCategory = $this->scheduleCategoryRepo->save($request->input());

        Session::flash('message', trans('texts.created_schedule_category'));

        return redirect()->to($scheduleCategory->getRoute());
    }

    public function update(UpdateScheduleCategoryRequest $request)
    {
        $scheduleCategory = $this->scheduleCategoryRepo->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_schedule_category'));

        return redirect()->to($scheduleCategory->getRoute());
    }

    public function cloneScheduleCategory(ScheduleCategoryRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->scheduleCategoryService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_schedule_category" : "{$action}d_schedule_categories";
            $message = trans("texts.$field", ['count' => $count]);
            Session::flash('message', $message);
        }

        return redirect()->to('/schedule_categories');
    }
}
