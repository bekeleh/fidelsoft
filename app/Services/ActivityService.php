<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ActivityDatatable;
use App\Ninja\Repositories\ActivityRepository;

/**
 * Class ActivityService.
 */
class ActivityService extends BaseService
{
    protected $activityRepo;
    protected $datatableService;


    public function __construct(ActivityRepository $activityRepo, DatatableService $datatableService)
    {
        $this->activityRepo = $activityRepo;
        $this->datatableService = $datatableService;
    }


    public function getDatatable($clientPublicId = null)
    {
        $clientId = Client::getPrivateId($clientPublicId);

        $query = $this->activityRepo->findByClientId($clientId);

        return $this->datatableService->createDatatable(new ActivityDatatable(false), $query);
    }
}
