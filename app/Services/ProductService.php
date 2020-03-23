<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Ninja\Datatables\ItemCategoryDatatable;
use App\Ninja\Datatables\UnitDatatable;
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

    public function save($data, $product = null)
    {
        return $this->productRepo->save($data, $product);
    }

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

    public function getDatatableUnit($unitPublicId)
    {
        $datatable = new UnitDatatable(true, true);

        $query = $this->productRepo->findUnit($unitPublicId);

        if (!Utils::hasPermission('view_unit')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
