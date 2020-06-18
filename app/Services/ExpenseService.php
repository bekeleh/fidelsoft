<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ExpenseDatatable;
use App\Ninja\Repositories\ExpenseRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class ExpenseService extends BaseService
{

    protected $expenseRepo;
    protected $datatableService;

    public function __construct(ExpenseRepository $expenseRepo, DatatableService $datatableService)
    {
        $this->expenseRepo = $expenseRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->expenseRepo;
    }


    public function save($data, $expense = null)
    {
        return $this->expenseRepo->save($data, $expense);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->expenseRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_expense')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new ExpenseDatatable(), $query);
    }

    public function getDatatableVendor($vendorPublicId)
    {
        $datatable = new ExpenseDatatable(true, true);

        $query = $this->expenseRepo->findVendor($vendorPublicId);

        if (!Utils::hasPermission('view_expense')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableClient($clientPublicId)
    {
        $datatable = new ExpenseDatatable(true, true);

        $query = $this->expenseRepo->findClient($clientPublicId);

        if (!Utils::hasPermission('view_clients')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
