<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\RecurringPurchaseInvoiceDatatable;
use App\Ninja\Repositories\PurchaseInvoiceRepository;

/**
 * Class RecurringPurchaseInvoiceController.
 */
class RecurringPurchaseInvoiceController extends BaseController
{

    protected $purchaseInvoiceRepo;


    public function __construct(PurchaseInvoiceRepository $purchaseInvoiceRepo)
    {
        //parent::__construct();

        $this->purchaseInvoiceRepo = $purchaseInvoiceRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_RECURRING_PURCHASE_INVOICE);
        $data = [
            'title' => trans('texts.recurring_purchase_invoices'),
            'entityType' => ENTITY_RECURRING_PURCHASE_INVOICE,
            'datatable' => new RecurringPurchaseInvoiceDatatable(),
        ];

        return response()->view('list_wrapper', $data);
    }
}
