<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemTransferDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Datatables\StatusDatatable;
use App\Ninja\Datatables\StoreDatatable;
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
        $datatable = new ItemTransferDatatable(true);
        $query = $this->itemTransferRepo->find($accountId, $search);
        if (!Utils::hasAccess('view_item_transfers')) {
            $query->where('item_transfers.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query, 'item_transfers');
    }

    public function getDatatableProduct($productPublicId)
    {
        $datatable = new ProductDatatable(true, true);

        $query = $this->itemTransferRepo->findProduct($productPublicId);

        if (!Utils::hasAccess('view_products')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'products');
    }

    public function getDatatableStore($storePublicId)
    {
        $datatable = new StoreDatatable(true, true);

        $query = $this->itemTransferRepo->findStore($storePublicId);

        if (!Utils::hasAccess('view_stores')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'stores');
    }

    public function getDatatableStatus($statusPublicId)
    {
        $datatable = new StatusDatatable(true, true);

        $query = $this->itemTransferRepo->findStore($statusPublicId);

        if (!Utils::hasAccess('view_statuses')) {
            $query->where('statuses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'statuses');
    }
}
