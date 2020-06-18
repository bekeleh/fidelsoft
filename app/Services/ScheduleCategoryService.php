<?php

namespace App\Services;

use App\Ninja\Datatables\ScheduleCategoryDatatable;
use App\Ninja\Repositories\ScheduleCategoryRepository;

/**
 * Class ExpenseCategoryService.
 */
class ScheduleCategoryService extends BaseService
{

    protected $scheduleCategoryRepo;
    protected $datatableService;

    public function __construct(ScheduleCategoryRepository $scheduleCategoryRepo, DatatableService $datatableService)
    {
        $this->scheduleCategoryRepo = $scheduleCategoryRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->scheduleCategoryRepo;
    }

    public function save($data)
    {
        return $this->scheduleCategoryRepo->save($data);
    }

    public function getDatatable($accountId, $search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ScheduleCategoryDatatable(true, true);

        $query = $this->scheduleCategoryRepo->find($accountId, $search);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
