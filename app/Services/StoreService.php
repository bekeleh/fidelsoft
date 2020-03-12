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

    /**
     * @return StoreRepository
     */
    protected function getRepo()
    {
        return $this->storeRepo;
    }

    /**
     * @param $data
     * @param null $store
     *
     * @return mixed|null
     */
    public function save($data, $store = null)
    {
        if (isset($data['location_id']) && $data['location_id']) {
            $data['location_id'] = Location::getPrivateId($data['location_id']);
        }
        if ($store) {
            $data['updated_by'] = auth::user()->username;
        } else {
            $data['created_by'] = auth::user()->username;
        }
        return $this->storeRepo->save($data, $store);
    }

    /**
     * @param $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search)
    {
        $query = $this->storeRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_store')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new StoreDatatable(), $query);
    }

    /**
     * @param $locationPublicId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->storeRepo->findLocation($locationPublicId);

        if (!Utils::hasPermission('view_location')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @param $clientPublicId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatableClient($clientPublicId)
    {
        $datatable = new StoreDatatable(true, true);

        $query = $this->storeRepo->findClient($clientPublicId);

        if (!Utils::hasPermission('view_client')) {
            $query->where('stores.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
