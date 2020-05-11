<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ManufacturerDatatable;
use App\Ninja\Repositories\ManufacturerRepository;

class ManufacturerService extends BaseService
{

    protected $datatableService;

    protected $manufacturerRepo;


    public function __construct(DatatableService $datatableService, ManufacturerRepository $manufacturerRepo)
    {
        $this->datatableService = $datatableService;
        $this->manufacturerRepo = $manufacturerRepo;
    }

    protected function getRepo()
    {
        return $this->manufacturerRepo;
    }

    public function save($data, $manufacturer = null)
    {
        return $this->manufacturerRepo->save($data, $manufacturer);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new ManufacturerDatatable(true);
        $query = $this->manufacturerRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_manufacturer')) {
            $query->where('manufacturers.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
