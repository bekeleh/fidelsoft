<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Ninja\Repositories\ScheduleRepository;
use App\Services\ScheduleService;

class ScheduleApiController extends BaseAPIController
{
    protected $ScheduleRepo;
    protected $ScheduleService;
    protected $entityType = ENTITY_SCHEDULE;

    public function __construct(ScheduleRepository $ScheduleRepo, ScheduleService $ScheduleService)
    {
        parent::__construct();

        $this->ScheduleRepo = $ScheduleRepo;
        $this->ScheduleService = $ScheduleService;
    }

    /**
     * @SWG\Get(
     *   path="/schedules",
     *   summary="List schedules",
     *   operationId="listSchedules",
     *   tags={"schedule"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of schedules",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $clients = Schedule::scope()
            ->orderBy('created_at', 'desc')
            ->withTrashed();

        return $this->listResponse($clients);
    }

    /**
     * @SWG\Get(
     *   path="/schedules/{schedule_id}",
     *   summary="Retrieve an Expense Report",
     *   operationId="getSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single schedule categroy",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ScheduleRequest $request
     * @return
     */
    public function show(ScheduleRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/schedules",
     *   summary="Create an schedule",
     *   operationId="createSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule",
     *     @SWG\Schema(ref="#/definitions/Schedule")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateScheduleRequest $request
     * @return
     */
    public function store(CreateScheduleRequest $request)
    {
        $Schedule = $this->ScheduleRepo->save($request->input());

        return $this->itemResponse($Schedule);
    }

    /**
     * @SWG\Put(
     *   path="/schedules/{schedule_id}",
     *   summary="Update an schedule",
     *   operationId="updateSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule",
     *     @SWG\Schema(ref="#/definitions/Schedule")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduleRequest $request
     * @return
     */
    public function update(UpdateScheduleRequest $request)
    {
        $Schedule = $this->ScheduleRepo->save($request->input(), $request->entity());

        return $this->itemResponse($Schedule);
    }

    /**
     * @SWG\Delete(
     *   path="/schedules/{schedule_id}",
     *   summary="Delete an schedule",
     *   operationId="deleteSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduleRequest $request
     * @return
     */
    public function destroy(UpdateScheduleRequest $request)
    {
        $entity = $request->entity();

        $this->ScheduleRepo->delete($entity);

        return $this->itemResponse($entity);
    }
}
