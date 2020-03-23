<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\UserDatatable;
use App\Ninja\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService.
 */
class UserService extends BaseService
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepo
     * @param DatatableService $datatableService
     */
    public function __construct(UserRepository $userRepo, DatatableService $datatableService)
    {
        $this->userRepo = $userRepo;
        $this->datatableService = $datatableService;
    }


    /**
     * @return UserRepository|null
     */
    protected function getRepo()
    {
        return $this->userRepo;
    }

    public function save($data, $user = null)
    {

        return $this->userRepo->save($data, $user);
    }

    public function getDatatable($accountId)
    {
        $datatable = new UserDatatable(false);
        $query = $this->userRepo->find($accountId);
        if (!Utils::hasPermission('view_user')) {
            $query->where('users.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }
}
