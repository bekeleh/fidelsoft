<?php

namespace App\Services;

use App\Ninja\Datatables\RecurringBillDatatable;
use App\Ninja\Repositories\BillRepository;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Utils;

class RecurringBillService extends BaseService
{
    protected $billRepo;
    protected $datatableService;

    public function __construct(BillRepository $billRepo, DatatableService $datatableService)
    {
        $this->billRepo = $billRepo;
        $this->datatableService = $datatableService;
    }

    public function getDatatable($accountId, $vendorPublicId, $entityType, $search)
    {
        $datatable = new RecurringBillDatatable(true, true);

        $query = $this->billRepo->getRecurringBills($accountId, $vendorPublicId, $entityType, $search);

        if (!Utils::hasPermission('view_bill')) {
            $query->where('bills.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
