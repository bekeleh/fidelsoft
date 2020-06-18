<?php

namespace App\Services;

use App\Ninja\Datatables\SubscriptionDatatable;
use App\Ninja\Repositories\SubscriptionRepository;

/**
 * Class SubscriptionService.
 */
class SubscriptionService extends BaseService
{

    protected $subscriptionRepo;
    protected $datatableService;

    public function __construct(SubscriptionRepository $subscriptionRepo, DatatableService $datatableService)
    {
        $this->subscriptionRepo = $subscriptionRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->subscriptionRepo;
    }

    public function getDatatable($accountId)
    {
        $datatable = new SubscriptionDatatable(true, true);
        $query = $this->subscriptionRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
