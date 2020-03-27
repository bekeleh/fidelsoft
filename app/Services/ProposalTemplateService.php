<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ProposalTemplateDatatable;
use App\Ninja\Repositories\ProposalTemplateRepository;

/**
 * Class ProposalTemplateService.
 */
class ProposalTemplateService extends BaseService
{

    protected $proposalTemplateRepo;
    protected $datatableService;

    public function __construct(ProposalTemplateRepository $proposalTemplateRepo, DatatableService $datatableService)
    {
        $this->proposalTemplateRepo = $proposalTemplateRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->proposalTemplateRepo;
    }

    public function save($data, $proposalTemplate = false)
    {
        return $this->proposalTemplateRepo->save($data, $proposalTemplate);
    }

    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalTemplateDatatable();

        $query = $this->proposalTemplateRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query, 'proposal_templates');
    }
}
