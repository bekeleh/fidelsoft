<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\StatusDatatable;
use App\Ninja\Repositories\StatusRepository;
use Illuminate\Support\Facades\Auth;

class StatusService extends BaseService
{
    protected $datatableService;
    protected $StatusRepo;

    public function __construct(DatatableService $datatableService, StatusRepository $StatusRepo)
    {
        $this->datatableService = $datatableService;
        $this->StatusRepo = $StatusRepo;
    }

    protected function getRepo()
    {
        return $this->StatusRepo;
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new StatusDatatable(true);
        $query = $this->StatusRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_status')) {
            $query->where('statuses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query, 'statuses');
    }
}
