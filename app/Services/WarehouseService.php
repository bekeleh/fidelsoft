<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Datatables\WarehouseDatatable;
use App\Ninja\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class WarehouseService.
 */
class WarehouseService extends BaseService
{

    protected $warehouseRepo;
    protected $datatableService;

    public function __construct(WarehouseRepository $warehouseRepo, DatatableService $datatableService)
    {
        $this->warehouseRepo = $warehouseRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->warehouseRepo;
    }

    public function save($data, $store = null)
    {
        return $this->warehouseRepo->save($data, $store);
    }


    public function getDatatable($accountId, $search)
    {
        $datatable = new WarehouseDatatable(true);

        $query = $this->warehouseRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_warehouse')) {
            $query->where('warehouses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->warehouseRepo->findLocation($locationPublicId);

        if (!Utils::hasPermission('view_location')) {
            $query->where('locations.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
