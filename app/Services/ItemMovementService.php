<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ItemMovementDatatable;
use App\Ninja\Repositories\ItemMovementRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExpenseService.
 */
class ItemMovementService extends BaseService
{
    /**
     * @var ItemMovementRepository
     */
    protected $itemMovementRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ExpenseService constructor.
     *
     * @param ItemMovementRepository $itemMovementRepo
     * @param DatatableService $datatableService
     */
    public function __construct(ItemMovementRepository $itemMovementRepo, DatatableService $datatableService)
    {
        $this->itemMovementRepo = $itemMovementRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return ItemMovementRepository
     */
    protected function getRepo()
    {
        return $this->itemMovementRepo;
    }

    /**
     * @param $data
     * @param null $store
     *
     * @return mixed|null
     */
    public function save($data, $store = null)
    {
        return $this->itemMovementRepo->save($data, $store);
    }

    /**
     * @param $search
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable($accountId, $search)
    {
        $query = $this->itemMovementRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_item_movement')) {
            $query->where('item_movements.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new ItemMovementDatatable(), $query);
    }

}
