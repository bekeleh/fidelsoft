<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\RecurringInvoiceDatatable;
use App\Ninja\Repositories\InvoiceRepository;

/**
 * Class RecurringInvoiceController.
 */
class RecurringInvoiceController extends BaseController
{

    protected $invoiceRepo;


    public function __construct(InvoiceRepository $invoiceRepo)
    {
        //parent::__construct();

        $this->invoiceRepo = $invoiceRepo;
    }

    public function index()
    {
        $data = [
            'title' => trans('texts.recurring_invoices'),
            'entityType' => ENTITY_RECURRING_INVOICE,
            'datatable' => new RecurringInvoiceDatatable(),
        ];

        return response()->view('list_wrapper', $data);
    }
}
