<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Product;
use App\Models\SaleType;
use App\Ninja\Datatables\ItemPriceDatatable;
use App\Ninja\Repositories\ItemPriceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class ItemPriceService extends BaseService
{
    /**
     * @var ItemPriceRepository
     */
    protected $itemPriceRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ExpenseService constructor.
     *
     * @param ItemPriceRepository $itemPriceRepo
     * @param DatatableService $datatableService
     */
    public function __construct(ItemPriceRepository $itemPriceRepo, DatatableService $datatableService)
    {
        $this->itemPriceRepo = $itemPriceRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return ItemPriceRepository
     */
    protected function getRepo()
    {
        return $this->itemPriceRepo;
    }

    /**
     * @param $data
     * @param null $itemPrice
     *
     * @return mixed|null
     */
    public function save($data, $itemPrice = null)
    {
        if ($itemPrice) {
            if (!empty($data['product_id'])) {
                $data['product_id'] = Product::getPrivateId($data['product_id']);
            }
            if (!empty($data['sale_type_id'])) {
                $data['sale_type_id'] = SaleType::getPrivateId($data['sale_type_id']);
            }
        }
        return $this->itemPriceRepo->save($data, $itemPrice);
    }

    /**
     * @param $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemPriceDatatable(true);
        $query = $this->itemPriceRepo->find($accountId, $search);
        if (!Utils::hasPermission('view_item_price')) {
            $query->where('item_prices.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableProduct($itemPublicId)
    {
        $datatable = new ItemPriceDatatable(true, true);

        $query = $this->itemPriceRepo->findItem($itemPublicId);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableSaleType($productPublicId)
    {
        $datatable = new ItemPriceDatatable(true, true);

        $query = $this->itemPriceRepo->findSaleType($productPublicId);

        if (!Utils::hasPermission('view_sale_type')) {
            $query->where('sale_types.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
