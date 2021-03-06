<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemBrandDatatable;
use App\Ninja\Repositories\ItemBrandRepository;
use Illuminate\Support\Facades\Auth;

class ItemBrandService extends BaseService
{

    protected $datatableService;
    protected $itemBrandRepo;


    public function __construct(DatatableService $datatableService, ItemBrandRepository $itemBrandRepo)
    {
        $this->datatableService = $datatableService;
        $this->itemBrandRepo = $itemBrandRepo;
    }

    protected function getRepo()
    {
        return $this->itemBrandRepo;
    }

    public function save($data, $itemBrand = null)
    {
        return $this->itemBrandRepo->save($data, $itemBrand);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemBrandDatatable(true, true);

        $query = $this->itemBrandRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_item_brand')) {
            $query->where('item_brands.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableItemCategory($itemCategoryPublicId)
    {
        $datatable = new ItemBrandDatatable(true, true);

        $query = $this->itemBrandRepo->findItemCategory($itemCategoryPublicId);

        if (!Utils::hasPermission('view_item_category')) {
            $query->where('item_categories.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
