<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\PermissionGroupDatatable;
use App\Ninja\Repositories\PermissionGroupRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionGroupService.
 */
class PermissionGroupService extends BaseService
{
    protected $groupRepo;
    protected $datatableService;

    public function __construct(PermissionGroupRepository $groupRepo, DatatableService $datatableService)
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

        if (!Utils::hasAccess('view_permission_groups')) {
            $query->where('permission_groups.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new PermissionGroupDatatable(), $query, 'permission_groups');
    }
}
