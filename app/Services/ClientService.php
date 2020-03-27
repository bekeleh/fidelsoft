<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\SaleType;
use App\Ninja\Datatables\ClientDatatable;
use App\Ninja\Datatables\HoldReasonDatatable;
use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\NinjaRepository;
use App\Policies\HoldReason;
use Illuminate\Support\Facades\Auth;

/**
 * Class ClientService.
 */
class ClientService extends BaseService
{

    protected $clientRepo;
    protected $datatableService;

    public function __construct(ClientRepository $clientRepo, DatatableService $datatableService, NinjaRepository $ninjaRepo)
    {
        $this->clientRepo = $clientRepo;
        $this->ninjaRepo = $ninjaRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->clientRepo;
    }

    public function save($data, $client = null)
    {
        if (Auth::user()->account->isNinjaAccount() && isset($data['plan'])) {
            $this->ninjaRepo->updatePlanDetails($data['public_id'], $data);
        }

        return $this->clientRepo->save($data, $client);
    }

    public function getDatatable($search, $accountId)
    {
        $datatable = new ClientDatatable();

        $query = $this->clientRepo->find($search, $accountId);

        return $this->datatableService->createDatatable($datatable, $query, 'clients');
    }

    public function getDatatableSaleType($saleTypePublicId)
    {
        $datatable = new SaleTypeDatatable(true, true);

        $query = $this->clientRepo->findSaleType($saleTypePublicId);

        if (!Utils::hasAccess('view_sale_types')) {
            $query->where('sale_types.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'sale_types');
    }

    public function getDatatableHoldReason($holdReasonPublicId)
    {
        $datatable = new HoldReasonDatatable(true, true);

        $query = $this->clientRepo->findHoldReason($holdReasonPublicId);

        if (!Utils::hasAccess('view_hold_reasons')) {
            $query->where('hold_reason.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'hold_reasons');
    }
}
