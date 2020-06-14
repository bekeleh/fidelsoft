<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleCategoryRequest;
use App\Http\Requests\ScheduleCategoryRequest;
use App\Http\Requests\UpdateScheduleCategoryRequest;
use App\Models\ScheduleCategory;
use App\Ninja\Repositories\ScheduleCategoryRepository;
use App\Services\ScheduleCategoryService;

class ScheduleCategoryApiController extends BaseAPIController
{
    protected $scheduleCategoryRepo;
    protected $scheduleCategoryService;
    protected $entityType = ENTITY_SCHEDULE_CATEGORY;

    public function __construct(ScheduleCategoryRepository $scheduleCategoryRepo, ScheduleCategoryService $scheduleCategoryService)
    {
        parent::__construct();

        $this->scheduleCategoryRepo = $scheduleCategoryRepo;
        $this->scheduleCategoryService = $scheduleCategoryService;
    }

    /**
     * @SWG\Get(
     *   path="/schedule_categories",
     *   summary="List schedule categories",
     *   operationId="listScheduleCategories",
     *   tags={"schedule_category"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of schedule categories",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ScheduleCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $clients = ScheduleCategory::Scope()
            ->orderBy('created_at', 'desc')
            ->withTrashed();

        return $this->listResponse($clients);
    }

    /**
     * @SWG\Get(
     *   path="/schedule_categories/{schedule_category_id}",
     *   summary="Retrieve an Expense Category",
     *   operationId="getScheduleCategory",
     *   tags={"schedule_category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single schedule categroy",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduleCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ScheduleCategoryRequest $request
     * @return
     */
    public function show(ScheduleCategoryRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/schedule_categories",
     *   summary="Create an schedule category",
     *   operationId="createScheduleCategory",
     *   tags={"schedule_category"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule_category",
     *     @SWG\Schema(ref="#/definitions/ScheduleCategory")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New schedule category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduleCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateScheduleCategoryRequest $request
     * @return
     */
    public function store(CreateScheduleCategoryRequest $request)
    {
        $scheduleCategory = $this->scheduleCategoryRepo->save($request->input());

        return $this->itemResponse($scheduleCategory);
    }

    /**
     * @SWG\Put(
     *   path="/schedule_categories/{schedule_category_id}",
     *   summary="Update an schedule category",
     *   operationId="updateScheduleCategory",
     *   tags={"schedule_category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule_category",
     *     @SWG\Schema(ref="#/definitions/ScheduleCategory")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated schedule category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduleCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduleCategoryRequest $request
     * @return
     */
    public function update(UpdateScheduleCategoryRequest $request)
    {
        $scheduleCategory = $this->scheduleCategoryRepo->save($request->input(), $request->entity());

        return $this->itemResponse($scheduleCategory);
    }

    /**
     * @SWG\Delete(
     *   path="/schedule_categories/{schedule_category_id}",
     *   summary="Delete an schedule category",
     *   operationId="deleteScheduleCategory",
     *   tags={"schedule_category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted schedule category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduleCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduleCategoryRequest $request
     * @return
     */
    public function destroy(UpdateScheduleCategoryRequest $request)
    {
        $entity = $request->entity();

        $this->scheduleCategoryRepo->delete($entity);

        return $this->itemResponse($entity);
    }
}
