<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ProposalCategoryDatatable;
use App\Ninja\Repositories\ProposalCategoryRepository;

/**
 * Class ProposalCategoryService.
 */
class ProposalCategoryService extends BaseService
{

    protected $proposalCategoryRepo;
    protected $datatableService;

    public function __construct(ProposalCategoryRepository $proposalCategoryRepo, DatatableService $datatableService)
    {
        $this->proposalCategoryRepo = $proposalCategoryRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->proposalCategoryRepo;
    }

    public function save($data, $proposalCategory = false)
    {
        return $this->proposalCategoryRepo->save($data, $proposalCategory);
    }

    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalCategoryDatatable();

        $query = $this->proposalCategoryRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query, 'proposal_categories');
    }
}
