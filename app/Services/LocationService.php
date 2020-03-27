<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Repositories\LocationRepository;
use Illuminate\Support\Facades\Auth;

class LocationService extends BaseService
{

    protected $datatableService;

    protected $locationRepo;


    public function __construct(DatatableService $datatableService, LocationRepository $locationRepo)
    {
        $this->datatableService = $datatableService;
        $this->locationRepo = $locationRepo;
    }

    protected function getRepo()
    {
        return $this->locationRepo;
    }

    public function save($data, $location = null)
    {
        return $this->locationRepo->save($data, $location);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new LocationDatatable(true);
        $query = $this->locationRepo->find($accountId, $search);

        if (!Utils::hasAccess('view_locations')) {
            $query->where('locations.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'locations');
    }
}
