<?php

namespace App\Services;

use App\Ninja\Datatables\ProposalSnippetDatatable;
use App\Ninja\Repositories\ProposalSnippetRepository;

/**
 * Class ProposalSnippetService.
 */
class ProposalSnippetService extends BaseService
{

    protected $proposalSnippetRepo;
    protected $datatableService;


    public function __construct(ProposalSnippetRepository $proposalSnippetRepo, DatatableService $datatableService)
    {
        $this->proposalSnippetRepo = $proposalSnippetRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->proposalSnippetRepo;
    }

    public function save($data, $proposalSnippet = false)
    {
        return $this->proposalSnippetRepo->save($data, $proposalSnippet);
    }

    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalSnippetDatatable(true, true);

        $query = $this->proposalSnippetRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
