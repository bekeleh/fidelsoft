<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\BranchDatatable;
use App\Ninja\Repositories\BranchRepository;
use Illuminate\Support\Facades\Auth;

class BranchService extends BaseService
{

    protected $datatableService;

    protected $branchRepo;


    public function __construct(DatatableService $datatableService, BranchRepository $branchRepo)
    {
        $this->datatableService = $datatableService;
        $this->branchRepo = $branchRepo;
    }

    protected function getRepo()
    {
        return $this->branchRepo;
    }

    public function save($data, $branch = null)
    {
        return $this->branchRepo->save($data, $branch);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new BranchDatatable(true);
        $query = $this->branchRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_branch')) {
            $query->where('branches.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
