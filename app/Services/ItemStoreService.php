<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemStoreDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\ItemStoreRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class ItemStoreService extends BaseService
{

    protected $itemStoreRepo;
    protected $datatableService;

    public function __construct(ItemStoreRepository $itemStoreRepo, DatatableService $datatableService)
    {
        $this->itemStoreRepo = $itemStoreRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->itemStoreRepo;
    }

    public function save($data, $store = null)
    {
        return $this->itemStoreRepo->save($data, $store);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemStoreDatatable(true);
        $query = $this->itemStoreRepo->find($accountId, $search);
        if (!Utils::hasAccess('view_item_stores')) {
            $query->where('item_stores.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query, 'item_stores');
    }

    public function getDatatableProduct($productPublicId)
    {
        $datatable = new ProductDatatable(true, true);

        $query = $this->itemStoreRepo->findProduct($productPublicId);

        if (!Utils::hasAccess('view_products')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'products');
    }

    public function getDatatableStore($storePublicId)
    {
        $datatable = new StoreDatatable(true, true);

        $query = $this->itemStoreRepo->findStore($storePublicId);

        if (!Utils::hasAccess('view_stores')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'stores');
    }

}
