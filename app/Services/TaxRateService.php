<?php

namespace App\Services;

use App\Libraries\Utils;
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

    public function save($data, $taxRate = null)
    {
        return $this->taxRateRepo->save($data, $taxRate);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new TaxRateDatatable(true);

        $query = $this->taxRateRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_tax_rate')) {
            $query->where('tax_rates.user_id', '=', Auth::user()->id);
        }
        return $this->datatableService->createDatatable($datatable, $query);
    }
}
