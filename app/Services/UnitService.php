<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\UnitDatatable;
use App\Ninja\Repositories\UnitRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseCategoryService.
 */
class UnitService extends BaseService
{
    /**
     * @var UnitRepository
     */
    protected $unitRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param UnitRepository $unitRepo
     * @param DatatableService $datatableService
     */
    public function __construct(UnitRepository $unitRepo, DatatableService $datatableService)
    {
        $this->unitRepo = $unitRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return UnitRepository
     */
    protected function getRepo()
    {
        return $this->unitRepo;
    }

    /**
     * @param $data
     *
     * @return mixed|null
     */
    public function save($data)
    {
        return $this->unitRepo->save($data);
    }

    /**
     * @param $search
     *
     * @return JsonResponse
     */
    public function getDatatable($accountId, $search)
    {
        $query = $this->unitRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_unit')) {
            $query->where('units.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new UnitDatatable(), $query);
    }
}
