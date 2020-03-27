<?php

namespace App\Services;

use App\Ninja\Datatables\TaxRateDatatable;
use App\Ninja\Repositories\TaxRateRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class TaxRateService.
 */
class TaxRateService extends BaseService
{

    protected $taxRateRepo;
    protected $datatableService;

    public function __construct(TaxRateRepository $taxRateRepo, DatatableService $datatableService)
    {
        $this->taxRateRepo = $taxRateRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->taxRateRepo;
    }

    public function getDatatable($accountId)
    {
        $datatable = new TaxRateDatatable(false);
        $query = $this->taxRateRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query, 'tax_rates');
    }
}
