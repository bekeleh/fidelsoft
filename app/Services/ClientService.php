<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ClientDatatable;
use App\Ninja\Datatables\ClientTypeDatatable;
use App\Ninja\Datatables\HoldReasonDatatable;
use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\NinjaRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ClientService.
 */
class ClientService extends BaseService
{

    protected $clientRepo;
    protected $datatableService;
    private $ninjaRepo;

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

    public function getById($publicId, $accountId)
    {
        return $this->clientRepo->getById($publicId, $accountId);
    }

    public function save($data, $client = null)
    {
        if (Auth::user()->account->isNinjaAccount() &&  isset($data['plan']) && 
            isset($data['account_id'])) 
        {
            $this->ninjaRepo->updatePlanDetails($data['account_id'], $data);
        }

        return $this->clientRepo->save($data, $client);
    }


    public function getDatatable($accountId, $search)
    {
        $datatable = new ClientDatatable(true, true);
        $query = $this->clientRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_client')) {
            $query->where('clients.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    // public function getDatatableClientType($clientTypePublicId)
    // {
    //     $datatable = new ClientTypeDatatable(true, true);

    //     $query = $this->clientRepo->findClientType($clientTypePublicId);

    //     if (!Utils::hasPermission('view_client_type')) {
    //         $query->where('client_types.user_id', '=', Auth::user()->id);
    //     }

    //     return $this->datatableService->createDatatable($datatable, $query);
    // }

    // public function getDatatableSaleType($saleTypePublicId)
    // {
    //     $datatable = new SaleTypeDatatable(true, true);

    //     $query = $this->clientRepo->findSaleType($saleTypePublicId);

    //     if (!Utils::hasPermission('view_sale_type')) {
    //         $query->where('sale_types.user_id', '=', Auth::user()->id);
    //     }

    //     return $this->datatableService->createDatatable($datatable, $query);
    // }

    // public function getDatatableHoldReason($holdReasonPublicId)
    // {
    //     $datatable = new HoldReasonDatatable(true, true);

    //     $query = $this->clientRepo->findHoldReason($holdReasonPublicId);

    //     if (!Utils::hasPermission('view_hold_reason')) {
    //         $query->where('hold_reasons.user_id', '=', Auth::user()->id);
    //     }

    //     return $this->datatableService->createDatatable($datatable, $query);
    // }

}
