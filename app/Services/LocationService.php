<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Repositories\LocationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class LocationService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var LocationRepository
     */
    protected $locationRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService $datatableService
     * @param LocationRepository $locationRepo
     */
    public function __construct(DatatableService $datatableService, LocationRepository $locationRepo)
    {
        $this->datatableService = $datatableService;
        $this->locationRepo = $locationRepo;
    }

    /**
     * @return LocationRepository
     */
    protected function getRepo()
    {
        return $this->locationRepo;
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
        $datatable = new LocationDatatable(true);
        $query = $this->locationRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_location')) {
            $query->where('locations.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
