<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ProjectDatatable;
use App\Ninja\Repositories\ProjectRepository;

/**
 * Class ProjectService.
 */
class ProjectService extends BaseService
{

    protected $projectRepo;
    protected $datatableService;

    public function __construct(ProjectRepository $projectRepo, DatatableService $datatableService)
    {
        $this->projectRepo = $projectRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->projectRepo;
    }

    public function save($data, $project = false)
    {
        if (isset($data['client_id']) && $data['client_id']) {
            $data['client_id'] = Client::getPrivateId($data['client_id']);
        }

        return $this->projectRepo->save($data, $project);
    }

    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProjectDatatable(true, true);

        $query = $this->projectRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
