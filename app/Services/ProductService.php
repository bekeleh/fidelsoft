<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemBrandDatatable;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;

class ProductService extends BaseService
{

    protected $datatableService;
    protected $productRepo;


    public function __construct(DatatableService $datatableService, ProductRepository $productRepo)
    {
        $this->datatableService = $datatableService;
        $this->productRepo = $productRepo;
    }

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
        $datatable = new ProductDatatable(true, true);

        $query = $this->productRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableItemBrand($itemBrandPublicId)
    {
        $datatable = new ItemBrandDatatable(true, true);

        $query = $this->productRepo->findItemBrand($itemBrandPublicId);

        if (!Utils::hasPermission('view_item_brand')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

//    public function getDatatableUnit($unitPublicId)
//    {
//        $datatable = new UnitDatatable(true, true);
//
//        $query = $this->productRepo->findUnit($unitPublicId);
//
//        if (!Utils::hasPermission('view_unit')) {
//            $query->where('item_brands.user_id', '=', Auth::user()->id);
//        }
//
//        return $this->datatableService->createDatatable($datatable, $query);
//    }

}
