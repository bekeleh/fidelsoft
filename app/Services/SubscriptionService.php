<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\SubscriptionDatatable;
use App\Ninja\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\Auth;

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

    public function getDatatable($accountId, $search = null)
    {

        $query = $this->subscriptionRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_subscription')) {
            $query->where('subscriptions.user_id', '=', Auth::user()->id);
        }

        $datatable = new SubscriptionDatatable(true, true);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
