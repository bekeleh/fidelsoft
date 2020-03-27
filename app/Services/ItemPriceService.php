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

    protected $itemPriceRepo;
    protected $datatableService;

    public function __construct(ItemPriceRepository $itemPriceRepo, DatatableService $datatableService)
    {
        $this->itemPriceRepo = $itemPriceRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->itemPriceRepo;
    }

    public function save($data, $itemPrice = null)
    {
        return $this->itemPriceRepo->save($data, $itemPrice);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemPriceDatatable(true);
        $query = $this->itemPriceRepo->find($accountId, $search);
        if (!Utils::hasAccess('view_item_prices')) {
            $query->where('item_prices.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query, 'item_prices');
    }

    public function getDatatableProduct($itemPublicId)
    {
        $datatable = new ItemPriceDatatable(true, true);

        $query = $this->itemPriceRepo->findItem($itemPublicId);

        if (!Utils::hasAccess('view_products')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'products');
    }

    public function getDatatableSaleType($productPublicId)
    {
        $datatable = new ItemPriceDatatable(true, true);

        $query = $this->itemPriceRepo->findSaleType($productPublicId);

        if (!Utils::hasAccess('view_sale_types')) {
            $query->where('sale_types.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'sale_types');
    }

}
