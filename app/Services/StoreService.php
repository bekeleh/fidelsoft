<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class StoreService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var StoreRepository
     */
    protected $storeRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService $datatableService
     * @param StoreRepository $storeRepo
     */
    public function __construct(DatatableService $datatableService, StoreRepository $storeRepo)
    {
        $this->datatableService = $datatableService;
        $this->storeRepo = $storeRepo;
    }

    /**
     * @return StoreRepository
     */
    protected function getRepo()
    {
        return $this->storeRepo;
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
        $datatable = new StoreDatatable(true);
        $query = $this->storeRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_store')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
