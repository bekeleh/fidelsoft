<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\VendorCreditDatatable;
use App\Ninja\Repositories\VendorCreditRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class VendorCreditService.
 */
class VendorCreditService extends BaseService
{

    protected $creditRepo;
    protected $datatableService;

    public function __construct(VendorCreditRepository $creditRepo, DatatableService $datatableService)
    {
        $this->creditRepo = $creditRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->creditRepo;
    }

    public function save($data, $credit = null)
    {
        return $this->creditRepo->save($data, $credit);
    }

    public function getDatatable($vendorPublicId, $search)
    {
        // we don't support bulk edit and hide the vendor on the individual client page
        $datatable = new VendorCreditDatatable(true, true);

        $query = $this->creditRepo->find($vendorPublicId, $search);

        if (!Utils::hasPermission('view_vendor_credit')) {
            $query->where('vendor_credits.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
