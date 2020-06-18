<?php

namespace App\Services;

use App\Ninja\Datatables\ProposalDatatable;
use App\Ninja\Repositories\ProposalRepository;

/**
 * Class ProposalService.
 */
class ProposalService extends BaseService
{

    protected $proposalRepo;
    protected $datatableService;

    public function __construct(ProposalRepository $proposalRepo, DatatableService $datatableService)
    {
        $this->proposalRepo = $proposalRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->proposalRepo;
    }

    public function save($data, $proposal = false)
    {
        return $this->proposalRepo->save($data, $proposal);
    }

    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalDatatable(true, true);

        $query = $this->proposalRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
