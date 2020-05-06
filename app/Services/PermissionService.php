<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\PermissionDatatable;
use App\Ninja\Repositories\PermissionRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionService.
 */
class PermissionService extends BaseService
{

    protected $permissionRepo;
    protected $datatableService;


    public function __construct(PermissionRepository $permissionRepo, DatatableService $datatableService)
    {
        $this->permissionRepo = $permissionRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->permissionRepo;
    }

    public function save($data)
    {
        return $this->permissionRepo->save($data);
    }


    public function getDatatable($accountId, $search)
    {
        $query = $this->permissionRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_permission')) {
            $query->where('permissions.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new PermissionDatatable(), $query, 'permissions');
    }
}
