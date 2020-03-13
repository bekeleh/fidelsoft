<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Product;
use App\Models\Store;
use App\Ninja\Datatables\ItemStoreDatatable;
use App\Ninja\Repositories\ItemStoreRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class ItemStoreService extends BaseService
{
    /**
     * @var ItemStoreRepository
     */
    protected $itemStoreRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ExpenseService constructor.
     *
     * @param ItemStoreRepository $itemStoreRepo
     * @param DatatableService $datatableService
     */
    public function __construct(ItemStoreRepository $itemStoreRepo, DatatableService $datatableService)
    {
        $this->itemStoreRepo = $itemStoreRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return ItemStoreRepository
     */
    protected function getRepo()
    {
        return $this->itemStoreRepo;
    }

    /**
     * @param $data
     * @param null $store
     *
     * @return mixed|null
     */
    public function save($data, $store = null)
    {
//        if (isset($data['product_id']) && $data['product_id']) {
//            $data['product_id'] = Product::getPrivateId($data['product_id']);
//        }
//        if (isset($data['store_id']) && $data['store_id']) {
//            $data['store_id'] = Store::getPrivateId($data['store_id']);
//        }
        return $this->itemStoreRepo->save($data, $store);
    }

    /**
     * @param $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemStoreDatatable(true);
        $query = $this->itemStoreRepo->find($accountId, $search);
        if (!Utils::hasPermission('view_item_store')) {
            $query->where('item_stores.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @param $productPublicId
     * @return JsonResponse
     */
    public function getDatatableProduct($productPublicId)
    {
        $datatable = new ProductDatatable(true, true);

        $query = $this->itemStoreRepo->findProduct($productPublicId);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @param $storePublicId
     * @return JsonResponse
     */
    public function getDatatableStore($storePublicId)
    {
        $datatable = new StoreDatatable(true, true);

        $query = $this->itemStoreRepo->findStore($storePublicId);

        if (!Utils::hasPermission('view_store')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
