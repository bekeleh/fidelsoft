<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\ItemCategory;
use App\Ninja\Datatables\ItemCategoryDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProductService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var ProductRepository
     */
    protected $productRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService $datatableService
     * @param ProductRepository $productRepo
     */
    public function __construct(DatatableService $datatableService, ProductRepository $productRepo)
    {
        $this->datatableService = $datatableService;
        $this->productRepo = $productRepo;
    }

    /**
     * @return ProductRepository
     */
    protected function getRepo()
    {
        return $this->productRepo;
    }

    /**
     * @param $data
     * @param null $product
     *
     * @return mixed|null
     */
    public function save($data, $product = null)
    {
        if (isset($data['category_id']) && $data['category_id']) {
            $data['category_id'] = ItemCategory::getPrivateId($data['category_id']);
        }
        return $this->productRepo->save($data, $product);
    }

    /**
     * @param $accountId
     * @param mixed $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search)
    {
        $datatable = new ProductDatatable(true);
        $query = $this->productRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableItemCategory($itemCategoryPublicId)
    {
        $datatable = new ItemCategoryDatatable(true, true);

        $query = $this->productRepo->findItemCategory($itemCategoryPublicId);

        if (!Utils::hasPermission('view_item_category')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
