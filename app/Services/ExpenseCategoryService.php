<?php

namespace App\Services;

use App\Ninja\Datatables\ExpenseCategoryDatatable;
use App\Ninja\Repositories\ExpenseCategoryRepository;

/**
 * Class ExpenseCategoryService.
 */
class ExpenseCategoryService extends BaseService
{

    protected $categoryRepo;
    protected $datatableService;

    public function __construct(ExpenseCategoryRepository $categoryRepo, DatatableService $datatableService)
    {
        $this->categoryRepo = $categoryRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->categoryRepo;
    }

    public function save($data)
    {
        return $this->categoryRepo->save($data);
    }

    public function getDatatable($accountId, $search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ExpenseCategoryDatatable();

        $query = $this->categoryRepo->find($search);

        return $this->datatableService->createDatatable($datatable, $query, 'expense_categories');
    }
}
