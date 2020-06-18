<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ClientTypeDatatable;
use App\Ninja\Repositories\clientTypeRepository;
use Illuminate\Support\Facades\Auth;

class ClientTypeService extends BaseService
{

    protected $datatableService;
    protected $clientTypeRepo;

    public function __construct(DatatableService $datatableService, clientTypeRepository $clientTypeRepo)
    {
        $this->datatableService = $datatableService;
        $this->clientTypeRepo = $clientTypeRepo;
    }

    protected function getRepo()
    {
        return $this->clientTypeRepo;
    }

    public function save($data, $saleType = null)
    {
        return $this->clientTypeRepo->save($data, $saleType);
    }

    public function getDatatable($accountId, $search = null)
    {
        $datatable = new ClientTypeDatatable(true, true);
        $query = $this->clientTypeRepo->find($accountId, $search);
        if (!Utils::hasPermission('view_client_type')) {

            $query->where('client_types.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
