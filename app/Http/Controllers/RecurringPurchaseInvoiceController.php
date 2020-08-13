<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\RecurringBillDatatable;
use App\Ninja\Repositories\BillRepository;

/**
 * Class RecurringBillController.
 */
class RecurringBillController extends BaseController
{

    protected $BillRepo;


    public function __construct(BillRepository $BillRepo)
    {
        //parent::__construct();

        $this->BillRepo = $BillRepo;
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
