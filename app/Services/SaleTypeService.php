<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\SaleTypeRepository;
use Illuminate\Support\Facades\Auth;

class SaleTypeService extends BaseService
{

    protected $datatableService;
    protected $saleTypeRepo;

    public function __construct(DatatableService $datatableService, SaleTypeRepository $saleTypeRepo)
    {
        $this->datatableService = $datatableService;
        $this->saleTypeRepo = $saleTypeRepo;
    }

    protected function getRepo()
    {
        return $this->saleTypeRepo;
    }

    public function save($data, $saleType = null)
    {
        return $this->saleTypeRepo->save($data, $saleType);
    }

    public function getDatatable($accountId, $search = null)
    {
        $datatable = new SaleTypeDatatable(true);
        $query = $this->saleTypeRepo->find($accountId, $search);
        if (!Utils::hasAccess('view_sales_types')) {
            $query->where('sales_type.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'sales_types');
    }
}
