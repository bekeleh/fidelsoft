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

    protected $itemMovementRepo;
    protected $datatableService;

    public function __construct(ItemMovementRepository $itemMovementRepo, DatatableService $datatableService)
    {
        $this->itemMovementRepo = $itemMovementRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->itemMovementRepo;
    }

    public function save($data, $store = null)
    {
        return $this->itemMovementRepo->save($data, $store);
    }

    public function getDatatable($accountId, $search)
    {
        $query = $this->itemMovementRepo->find($accountId, $search);

        if (!Utils::hasAccess('view_item_movements')) {
            $query->where('item_movements.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new ItemMovementDatatable(), $query, 'view_item_movements');
    }

}
