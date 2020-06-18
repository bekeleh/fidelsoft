<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemPriceDatatable;
use App\Ninja\Repositories\ItemPriceRepository;
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
        $datatable = new ItemPriceDatatable(true, true);
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
