<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ApprovalStatusDatatable;
use App\Ninja\Repositories\ApprovalStatusRepository;
use Illuminate\Support\Facades\Auth;

class ApprovalStatusService extends BaseService
{
    protected $datatableService;
    protected $approvalStatusRepo;

    public function __construct(DatatableService $datatableService, ApprovalStatusRepository $approvalStatusRepo)
    {
        $this->datatableService = $datatableService;
        $this->approvalStatusRepo = $approvalStatusRepo;
    }

    protected function getRepo()
    {
        return $this->approvalStatusRepo;
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ApprovalStatusDatatable(true);
        $query = $this->approvalStatusRepo->find($accountId, $search);

        if (!Utils::hasAccess('view_approval_statuses')) {
            $query->where('approval_statuses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'approval_statuses');
    }
}
