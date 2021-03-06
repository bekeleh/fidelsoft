<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\HoldReasonDatatable;
use App\Ninja\Repositories\HoldReasonRepository;
use Illuminate\Support\Facades\Auth;

class HoldReasonService extends BaseService
{

    protected $datatableService;

    protected $holdReasonRepo;


    public function __construct(DatatableService $datatableService, HoldReasonRepository $holdReasonRepo)
    {
        $this->datatableService = $datatableService;
        $this->holdReasonRepo = $holdReasonRepo;
    }


    protected function getRepo()
    {
        return $this->holdReasonRepo;
    }


    public function getDatatable($accountId, $search)
    {
        $datatable = new HoldReasonDatatable(true, true);

        $query = $this->holdReasonRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_hold_reason')) {
            $query->where('hold_reasons.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
