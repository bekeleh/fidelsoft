<?php

namespace App\Services;

use App\Ninja\Datatables\ScheduledReportDatatable;
use App\Ninja\Repositories\ScheduledReportRepository;

/**
 * Class ScheduledReportService.
 */
class ScheduledReportService extends BaseService
{

    protected $ScheduledReportRepo;
    protected $datatableService;

    public function __construct(ScheduledReportRepository $ScheduledReportRepo, DatatableService $datatableService)
    {
        $this->ScheduledReportRepo = $ScheduledReportRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->ScheduledReportRepo;
    }

    public function save($data)
    {
        return $this->ScheduledReportRepo->save($data);
    }

    public function getDatatable($accountId, $search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ScheduledReportDatatable();

        $query = $this->ScheduledReportRepo->find($accountId, $search);

        return $this->datatableService->createDatatable($datatable, $query, 'scheduled_reports');
    }
}
