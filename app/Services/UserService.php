<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\LocationDatatable;
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
        return $this->userRepo->save($data, $user);
    }

    public function getDatatable($accountId, $search)
    {
        $query = $this->userRepo->find($accountId, $search);

//        if (! Utils::hasPermission('view_' . $entityType)) {
        if (!Utils::hasPermission('view_user')) {
            $query->where('users.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable(new UserDatatable(), $query);
    }

    public function getDatatableLocation($locationPublicId)
    {
        $datatable = new LocationDatatable(true, true);

        $query = $this->userRepo->findLocation($locationPublicId);

        if (!Utils::hasPermission('view_location')) {
            $query->where('locations.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
