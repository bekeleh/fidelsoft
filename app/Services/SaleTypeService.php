<?php

namespace App\Services;

use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\SaleTypeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Libraries\Utils;
use Exception;

class SaleTypeService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService $datatableService
     * @param SaleTypeRepository $saleTypeRepo
     */
    public function __construct(DatatableService $datatableService, SaleTypeRepository $saleTypeRepo)
    {
        $this->datatableService = $datatableService;
        $this->saleTypeRepo = $saleTypeRepo;
    }

    /**
     * @return SaleTypeRepository
     */
    protected function getRepo()
    {
        return $this->saleTypeRepo;
    }

    /**
     * @param $data
     * @param null $saleType
     *
     * @return mixed|null
     */
    public function save($data, $saleType = null)
    {
        return $this->saleTypeRepo->save($data, $saleType);
    }

    /**
     * @param $accountId
     * @param mixed $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search = null)
    {
        $datatable = new SaleTypeDatatable(true);
        $query = $this->saleTypeRepo->find($accountId, $search);
        if (!Utils::hasPermission('view_sales_type')) {
            $query->where('sales_type.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
