<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Vendor;
use App\Ninja\Datatables\ExpenseDatatable;
use App\Ninja\Repositories\ExpenseRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Utils;

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
        if (isset($data['client_id']) && $data['client_id']) {
            $data['client_id'] = Client::getPrivateId($data['client_id']);
        }

        if (isset($data['vendor_id']) && $data['vendor_id']) {
            $data['vendor_id'] = Vendor::getPrivateId($data['vendor_id']);
        }

        return $this->expenseRepo->save($data, $expense);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->expenseRepo->find($accountId, $search);

        if (!Utils::hasAccess('view_expenses')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new ExpenseDatatable(), $query, 'expenses');
    }

    public function getDatatableVendor($vendorPublicId)
    {
        $datatable = new ExpenseDatatable(true, true);

        $query = $this->expenseRepo->findVendor($vendorPublicId);

        if (!Utils::hasAccess('view_vendors')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'vendors');
    }

    public function getDatatableClient($clientPublicId)
    {
        $datatable = new ExpenseDatatable(true, true);

        $query = $this->expenseRepo->findClient($clientPublicId);

        if (!Utils::hasAccess('view_clients')) {
            $query->where('expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'clients');
    }

}
