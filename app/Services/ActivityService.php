<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Vendor;
use App\Ninja\Datatables\ActivityDatatable;
use App\Ninja\Repositories\ActivityRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ActivityService.
 */
class ActivityService extends BaseService
{
    /**
     * @var ActivityRepository
     */
    protected $activityRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ActivityService constructor.
     *
     * @param ActivityRepository $activityRepo
     * @param DatatableService $datatableService
     */
    public function __construct(ActivityRepository $activityRepo, DatatableService $datatableService)
    {
        $this->activityRepo = $activityRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param null $clientPublicId
     *
     * @return JsonResponse
     */
    public function getDatatable($clientPublicId = null, $search)
    {

        $datatable = new ActivityDatatable(false);

        $clientId = Client::getPrivateId($clientPublicId);


        $query = $this->activityRepo->findByClientId($clientId, $search);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getVendorDatatable($vendorPublicId = null, $search)
    {

        $datatable = new ActivityDatatable(false);

        $vendorId = Vendor::getPrivateId($vendorPublicId);


        $query = $this->activityRepo->findByVendorId($vendorId, $search);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
