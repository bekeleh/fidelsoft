<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\RecurringBillDatatable;
use App\Ninja\Repositories\BillRepository;

/**
 * Class RecurringBillController.
 */
class RecurringBillController extends BaseController
{

    protected $billRepo;


    public function __construct(BillRepository $billRepo)
    {
        //parent::__construct();

        $this->billRepo = $billRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_RECURRING_BILL);
        $data = [
            'title' => trans('texts.recurring_bills'),
            'entityType' => ENTITY_RECURRING_BILL,
            'datatable' => new RecurringBillDatatable(),
        ];

        return response()->view('list_wrapper', $data);
    }
}
