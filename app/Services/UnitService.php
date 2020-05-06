<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\UnitDatatable;
use App\Ninja\Repositories\UnitRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseCategoryService.
 */
class UnitService extends BaseService
{
    protected $unitRepo;
    protected $datatableService;


    public function __construct(UnitRepository $unitRepo, DatatableService $datatableService)
    {
        $this->unitRepo = $unitRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->unitRepo;
    }

    public function save($data)
    {
        return $this->unitRepo->save($data);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->unitRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_unit')) {
            $query->where('units.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new UnitDatatable(), $query, 'units');
    }
}
