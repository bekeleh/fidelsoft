<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\DepartmentDatatable;
use App\Ninja\Datatables\ItemRequestDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Datatables\WarehouseDatatable;
use App\Ninja\Repositories\ItemRequestRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ItemRequestService.
 */
class ItemRequestService extends BaseService
{

    protected $itemRequestRepo;
    protected $datatableService;

    public function __construct(ItemRequestRepository $itemRequestRepo, DatatableService $datatableService)
    {
        $this->itemRequestRepo = $itemRequestRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->itemRequestRepo;
    }

    public function save($data, $store = null)
    {
        return $this->itemRequestRepo->save($data, $store);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemRequestDatatable(true, true);

        $query = $this->itemRequestRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_item_request')) {
            $query->where('item_requests.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableProduct($productPublicId)
    {
        $datatable = new ProductDatatable(true, true);

        $query = $this->itemRequestRepo->findProduct($productPublicId);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableDepartment($departmentPublicId)
    {
        $datatable = new DepartmentDatatable(true, true);

        $query = $this->itemRequestRepo->findDepartment($departmentPublicId);

        if (!Utils::hasPermission('view_department')) {
            $query->where('departments.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableWarehouse($warehousePublicId)
    {
        $datatable = new WarehouseDatatable(true, true);

        $query = $this->itemRequestRepo->findWarehouse($warehousePublicId);

        if (!Utils::hasPermission('view_warehouse')) {
            $query->where('warehouses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    // public function getDatatableStatus($statusPublicId)
    // {
    //     $datatable = new StatusDatatable(true, true);

    //     $query = $this->itemRequestRepo->findStore($statusPublicId);

    //     if (!Utils::hasPermission('view_status')) {
    //         $query->where('statuses.user_id', '=', Auth::user()->id);
    //     }

    //     return $this->datatableService->createDatatable($datatable, $query);
    // }
}
