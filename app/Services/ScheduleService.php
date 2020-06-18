<?php

namespace App\Services;

use App\Ninja\Datatables\ScheduleDatatable;
use App\Ninja\Repositories\ScheduleRepository;

/**
 * Class ScheduledReportService.
 */
class ScheduleService extends BaseService
{

    protected $ScheduleRepo;
    protected $datatableService;

    public function __construct(ScheduleRepository $ScheduleRepo, DatatableService $datatableService)
    {
        $this->ScheduleRepo = $ScheduleRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->ScheduleRepo;
    }

    public function save($data)
    {
        return $this->ScheduleRepo->save($data);
    }

    public function getDatatable($accountId, $search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ScheduleDatatable();

        $query = $this->ScheduleRepo->find($accountId, $search);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
