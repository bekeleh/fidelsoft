<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\TokenDatatable;
use App\Ninja\Repositories\TokenRepository;

/**
 * Class TokenService.
 */
class TokenService extends BaseService
{

    protected $tokenRepo;
    protected $datatableService;

    public function __construct(TokenRepository $tokenRepo, DatatableService $datatableService)
    {
        $this->tokenRepo = $tokenRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->tokenRepo;
    }

    public function getDatatable($accountId, $search = null)
    {
        $query = $this->tokenRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_token')) {
            $query = $query->where('tokens.user_id', auth::user()->id);
        }

        $datatable = new TokenDatatable(true, true);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
