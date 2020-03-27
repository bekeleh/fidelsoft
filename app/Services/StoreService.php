<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class StoreService extends BaseService
{

    protected $storeRepo;
    protected $datatableService;

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

        if (!Utils::hasAccess('view_stores')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new StoreDatatable(), $query, 'stores');
    }

    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->storeRepo->findLocation($locationPublicId);

        if (!Utils::hasAccess('view_locations')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'locations');
    }

}
