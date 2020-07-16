<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemTransferDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Datatables\StatusDatatable;
use App\Ninja\Datatables\WarehouseDatatable;
use App\Ninja\Repositories\ItemTransferRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ItemTransferService.
 */
class ItemTransferService extends BaseService
{

    protected $itemTransferRepo;
    protected $datatableService;

    public function __construct(ItemTransferRepository $itemTransferRepo, DatatableService $datatableService)
    {
        $this->itemTransferRepo = $itemTransferRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->itemTransferRepo;
    }

    public function save($data, $store = null)
    {
        return $this->itemTransferRepo->save($data, $store);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemTransferDatatable(true, true);

        $query = $this->itemTransferRepo->find($accountId, $search);
        
        if (!Utils::hasPermission('view_item_transfer')) {
            $query->where('item_transfers.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableProduct($productPublicId)
    {
        $datatable = new ProductDatatable(true, true);

        $query = $this->itemTransferRepo->findProduct($productPublicId);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableStore($storePublicId)
    {
        $datatable = new WarehouseDatatable(true, true);

        $query = $this->itemTransferRepo->findStore($storePublicId);

        if (!Utils::hasPermission('view_store')) {
            $query->where('warehouses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableStatus($statusPublicId)
    {
        $datatable = new StatusDatatable(true, true);

        $query = $this->itemTransferRepo->findStore($statusPublicId);

        if (!Utils::hasPermission('view_status')) {
            $query->where('statuses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
