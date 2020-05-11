<?php

namespace App\Services;

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
}
