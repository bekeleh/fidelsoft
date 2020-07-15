<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\CompanyDatatable;
use App\Ninja\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Auth;

class CompanyService extends BaseService
{

    protected $datatableService;

    protected $companyReasonRepo;


    public function __construct(DatatableService $datatableService, CompanyRepository $companyReasonRepo)
    {
        $this->datatableService = $datatableService;
        $this->companyReasonRepo = $companyReasonRepo;
    }


    protected function getRepo()
    {
        return $this->companyReasonRepo;
    }


    public function getDatatable($accountId, $search)
    {
        $datatable = new CompanyDatatable(true, true);

        $query = $this->companyReasonRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_company')) {
            $query->where('companies.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
