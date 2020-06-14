<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduledReportRequest;
use App\Http\Requests\ScheduledReportRequest;
use App\Http\Requests\UpdateScheduledReportRequest;
use App\Models\ScheduledReport;
use App\Ninja\Repositories\ScheduledReportRepository;
use App\Services\ScheduledReportService;

class ScheduledReportApiController extends BaseAPIController
{
    protected $ScheduledReportRepo;
    protected $ScheduledReportService;
    protected $entityType = ENTITY_SCHEDULED_REPORT;

    public function __construct(ScheduledReportRepository $ScheduledReportRepo, ScheduledReportService $ScheduledReportService)
    {
        parent::__construct();

        $this->ScheduledReportRepo = $ScheduledReportRepo;
        $this->ScheduledReportService = $ScheduledReportService;
    }

    /**
     * @SWG\Get(
     *   path="/SCHEDULED_REPORTs",
     *   summary="List schedule reports",
     *   operationId="listScheduledReports",
     *   tags={"SCHEDULED_REPORT"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of schedule reports",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ScheduledReport"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $clients = ScheduledReport::scope()
            ->orderBy('created_at', 'desc')
            ->withTrashed();

        return $this->listResponse($clients);
    }

    /**
     * @SWG\Get(
     *   path="/SCHEDULED_REPORTs/{SCHEDULED_REPORT_id}",
     *   summary="Retrieve an Expense Report",
     *   operationId="getScheduledReport",
     *   tags={"SCHEDULED_REPORT"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="SCHEDULED_REPORT_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single schedule categroy",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduledReport"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ScheduledReportRequest $request
     * @return
     */
    public function show(ScheduledReportRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/SCHEDULED_REPORTs",
     *   summary="Create an schedule report",
     *   operationId="createScheduledReport",
     *   tags={"SCHEDULED_REPORT"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="SCHEDULED_REPORT",
     *     @SWG\Schema(ref="#/definitions/ScheduledReport")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New schedule report",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduledReport"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateScheduledReportRequest $request
     * @return
     */
    public function store(CreateScheduledReportRequest $request)
    {
        $ScheduledReport = $this->ScheduledReportRepo->save($request->input());

        return $this->itemResponse($ScheduledReport);
    }

    /**
     * @SWG\Put(
     *   path="/SCHEDULED_REPORTs/{SCHEDULED_REPORT_id}",
     *   summary="Update an schedule report",
     *   operationId="updateScheduledReport",
     *   tags={"SCHEDULED_REPORT"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="SCHEDULED_REPORT_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="SCHEDULED_REPORT",
     *     @SWG\Schema(ref="#/definitions/ScheduledReport")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated schedule report",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduledReport"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduledReportRequest $request
     * @return
     */
    public function update(UpdateScheduledReportRequest $request)
    {
        $ScheduledReport = $this->ScheduledReportRepo->save($request->input(), $request->entity());

        return $this->itemResponse($ScheduledReport);
    }

    /**
     * @SWG\Delete(
     *   path="/SCHEDULED_REPORTs/{SCHEDULED_REPORT_id}",
     *   summary="Delete an schedule report",
     *   operationId="deleteScheduledReport",
     *   tags={"SCHEDULED_REPORT"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="SCHEDULED_REPORT_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted schedule report",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ScheduledReport"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateScheduledReportRequest $request
     * @return
     */
    public function destroy(UpdateScheduledReportRequest $request)
    {
        $entity = $request->entity();

        $this->ScheduledReportRepo->delete($entity);

        return $this->itemResponse($entity);
    }
}
