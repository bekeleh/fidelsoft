<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Vendor;
use App\Ninja\Datatables\VendorDatatable;
use App\Ninja\Repositories\NinjaRepository;
use App\Ninja\Repositories\VendorRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class VendorService.
 */
class VendorService extends BaseService
{

    protected $vendorRepo;
    protected $datatableService;

    public function __construct(
        VendorRepository $vendorRepo,
        DatatableService $datatableService,
        NinjaRepository $ninjaRepo
    )
    {
        $this->vendorRepo = $vendorRepo;
        $this->ninjaRepo = $ninjaRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->vendorRepo;
    }

    public function save(array $data, Vendor $vendor = null)
    {
        return $this->vendorRepo->save($data, $vendor);
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new VendorDatatable(true, true);
        $query = $this->vendorRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_vendor')) {
            $query->where('vendors.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

}
