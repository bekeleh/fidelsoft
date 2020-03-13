<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemCategoryDatatable;
use App\Ninja\Repositories\ItemCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class ItemCategoryService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var ItemCategoryRepository
     */
    protected $itemCategoryRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService $datatableService
     * @param ItemCategoryRepository $itemCategoryRepo
     */
    public function __construct(DatatableService $datatableService, ItemCategoryRepository $itemCategoryRepo)
    {
        $this->datatableService = $datatableService;
        $this->itemCategoryRepo = $itemCategoryRepo;
    }

    /**
     * @return ItemCategoryRepository
     */
    protected function getRepo()
    {
        return $this->itemCategoryRepo;
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
        $datatable = new ItemCategoryDatatable(true);
        $query = $this->itemCategoryRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_item_category')) {
            $query->where('item_categories.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
