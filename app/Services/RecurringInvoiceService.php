<?php

namespace App\Services;

use App\Ninja\Datatables\RecurringInvoiceDatatable;
use App\Ninja\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Utils;

class RecurringInvoiceService extends BaseService
{
    protected $invoiceRepo;
    protected $datatableService;

    public function __construct(InvoiceRepository $invoiceRepo, DatatableService $datatableService)
    {
        $this->invoiceRepo = $invoiceRepo;
        $this->datatableService = $datatableService;
    }

    public function getDatatable($accountId, $clientPublicId, $entityType, $search)
    {
        $datatable = new RecurringInvoiceDatatable(true, true);

        $query = $this->invoiceRepo->getRecurringInvoices($accountId, $clientPublicId, $entityType, $search);

        if (!Utils::hasPermission('view_invoice')) {
            $query->where('invoices.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
