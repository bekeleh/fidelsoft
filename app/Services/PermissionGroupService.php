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
    protected $permissionGroupRepo;
    protected $datatableService;

    public function __construct(PermissionGroupRepository $permissionGroupRepo, DatatableService $datatableService)
    {
        $this->permissionGroupRepo = $permissionGroupRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->permissionGroupRepo;
    }

    public function save($data)
    {
        return $this->permissionGroupRepo->save($data);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new PermissionGroupDatatable(true, true);

        $query = $this->permissionGroupRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_permission_group')) {
            $query->where('permission_groups.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
