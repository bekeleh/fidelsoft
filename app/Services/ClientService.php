<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ClientDatatable;
use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\NinjaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ClientService.
 */
class ClientService extends BaseService
{
    /**
     * @var ClientRepository
     */
    protected $clientRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ClientService constructor.
     *
     * @param ClientRepository $clientRepo
     * @param DatatableService $datatableService
     * @param NinjaRepository $ninjaRepo
     */
    public function __construct(ClientRepository $clientRepo, DatatableService $datatableService, NinjaRepository $ninjaRepo)
    {
        $this->clientRepo = $clientRepo;
        $this->ninjaRepo = $ninjaRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return ClientRepository
     */
    protected function getRepo()
    {
        return $this->clientRepo;
    }

    /**
     * @param $data
     * @param null $client
     *
     * @return mixed|null
     */
    public function save($data, $client = null)
    {
        if (Auth::user()->account->isNinjaAccount() && isset($data['plan'])) {
            $this->ninjaRepo->updatePlanDetails($data['public_id'], $data);
        }

        return $this->clientRepo->save($data, $client);
    }

    /**
     * @param $search
     * @param $userId
     *
     * @return JsonResponse
     */
    public function getDatatable($search, $userId)
    {
        $datatable = new ClientDatatable();

        $query = $this->clientRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @param $saleTypePublicId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatableSaleType($saleTypePublicId)
    {
        $datatable = new SaleTypeDatatable(true, true);

        $query = $this->clientRepo->findSaleType($saleTypePublicId);

        if (!Utils::hasPermission('view_sale_type')) {
            $query->where('sale_types.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
