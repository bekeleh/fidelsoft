<?php

namespace App\Services;

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

    public function getDatatable($userId)
    {
        $datatable = new TokenDatatable(true, true);
        $query = $this->tokenRepo->find($userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
