<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\GroupDatatable;
use App\Ninja\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class GroupService.
 */
class GroupService extends BaseService
{
    /**
     * @var GroupRepository
     */
    protected $groupRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;


    public function __construct(GroupRepository $groupRepo, DatatableService $datatableService)
    {
        $this->groupRepo = $groupRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->groupRepo;
    }

    public function save($data)
    {
        return $this->groupRepo->save($data);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->groupRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_group')) {
            $query->where('permission_groups.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new GroupDatatable(), $query);
    }
}
