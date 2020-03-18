<?php

namespace App\Services;

use App\Libraries\Utils;
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
     * @param null $store
     *
     * @return mixed|null
     */
    public function save($data, $store = null)
    {
        if ($store) {
            if (!empty($data['sale_type_id'])) {
                $data['sale_type_id'] = SaleType::getPrivateId($data['sale_type_id']);
            }
        }
        return $this->itemPriceRepo->save($data, $store);
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

    /**
     * @param $productPublicId
     * @return JsonResponse
     */
    public function getDatatableSaleType($productPublicId)
    {
        $datatable = new SaleTypeDatatable(true, true);

        $query = $this->itemPriceRepo->findSaleType($productPublicId);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
