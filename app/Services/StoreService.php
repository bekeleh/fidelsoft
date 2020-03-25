<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Location;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class StoreService extends BaseService
{
    /**
     * @var StoreRepository
     */
    protected $storeRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ExpenseService constructor.
     *
     * @param StoreRepository $storeRepo
     * @param DatatableService $datatableService
     */
    public function __construct(StoreRepository $storeRepo, DatatableService $datatableService)
    {
        $this->storeRepo = $storeRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->storeRepo;
    }

    public function save($data, $store = null)
    {
        return $this->storeRepo->save($data, $store);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->storeRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_store')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new StoreDatatable(), $query);
    }

    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->storeRepo->findLocation($locationPublicId);

        if (!Utils::hasPermission('view_location')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
