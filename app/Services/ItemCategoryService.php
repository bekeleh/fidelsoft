<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemCategoryDatatable;
use App\Ninja\Repositories\ItemCategoryRepository;
use Illuminate\Support\Facades\Auth;

class ItemCategoryService extends BaseService
{
    protected $datatableService;
    protected $itemCategoryRepo;

    public function __construct(DatatableService $datatableService, ItemCategoryRepository $itemCategoryRepo)
    {
        $this->datatableService = $datatableService;
        $this->itemCategoryRepo = $itemCategoryRepo;
    }

    protected function getRepo()
    {
        return $this->itemCategoryRepo;
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ItemCategoryDatatable(true);

        $query = $this->itemCategoryRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_item_category')) {
            $query->where('item_categories.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
