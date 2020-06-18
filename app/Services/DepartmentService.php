<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\DepartmentDatatable;
use App\Ninja\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\Auth;

class DepartmentService extends BaseService
{

    protected $datatableService;

    protected $departmentRepo;


    public function __construct(DatatableService $datatableService, DepartmentRepository $departmentRepo)
    {
        $this->datatableService = $datatableService;
        $this->departmentRepo = $departmentRepo;
    }

    protected function getRepo()
    {
        return $this->departmentRepo;
    }

    public function save($data, $department = null)
    {
        return $this->departmentRepo->save($data, $department);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new DepartmentDatatable(true, true);
        $query = $this->departmentRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_department')) {
            $query->where('departments.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
