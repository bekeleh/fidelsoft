<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Datatables\BranchDatatable;
use App\Ninja\Datatables\UserDatatable;
use App\Ninja\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService.
 */
class UserService extends BaseService
{

    protected $userRepo;

    protected $datatableService;


    public function __construct(UserRepository $userRepo, DatatableService $datatableService)
    {
        $this->userRepo = $userRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->userRepo;
    }

    public function getById($publicId, $accountId)
    {
        return $this->userRepo->getById($publicId, $accountId);
    }

    public function save($data, $user = null)
    {
        if (!$data) {
            return false;
        }

        return $this->userRepo->save($data, $user);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new UserDatatable(true, true);

        $query = $this->userRepo->find($accountId, $search);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->userRepo->findLocation($locationPublicId);
        if (!$query) {
            return false;
        }
        if (!Utils::hasPermission('view_location')) {
            $query->where('locations.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function getDatatableBranch($branchPublicId)
    {
        $datatable = new BranchDatatable(true, true);

        $query = $this->userRepo->findBranch($branchPublicId);

        if (!$query) {
            return false;
        }

        if (!Utils::hasPermission('view_branch')) {
            $query->where('branches.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
