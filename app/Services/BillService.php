<?php

namespace App\Services;

use App\Events\BillQuoteInvitationWasApproved;
use App\Jobs\DownloadBill;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\BillInvitation;
use App\Models\Bill;
use App\Ninja\Datatables\BillDatatable;
use App\Ninja\Repositories\VendorRepository;
use App\Ninja\Repositories\BillRepository;
use Illuminate\Support\Facades\Auth;

class BillService extends BaseService
{

    protected $vendorRepo;
    protected $billRepo;
    protected $datatableService;

    /**
     *
     * BillService constructor.
     *
     * @param VendorRepository $vendorRepo
     * @param BillRepository $billRepo
     * @param DatatableService $datatableService
     */
    public function __construct(
        VendorRepository $vendorRepo,
        BillRepository $billRepo,
        DatatableService $datatableService)
    {
        $this->vendorRepo = $vendorRepo;
        $this->billRepo = $billRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->billRepo;
    }

    public function bulk($ids, $action)
    {
        $user = Auth::user();
        if ($action == 'download') {
            $bills = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadBill($user, $bills));
            return count($bills);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    public function save(array $data, Bill $Bill = null)
    {

        if (!empty($data['vendor'])) {
            $canSaveVendor = false;
            $canViewVendor = false;
            $vendorPublicId = array_get($data, 'vendor.public_id') ?: array_get($data, 'vendor.id');
            if (empty($vendorPublicId) || intval($vendorPublicId) < 0) {
                $canSaveVendor = Auth::user()->can('create', ENTITY_VENDOR);
            } else {
                $vendor = Vendor::scope($vendorPublicId)->first();
                $canSaveVendor = Auth::user()->can('edit', $vendor);
                $canViewVendor = Auth::user()->can('view', $vendor);
            }
//          if new vendor is created
            if ($canSaveVendor) {
                $vendor = $this->vendorRepo->save($data['vendor']);
            }
            if ($canSaveVendor || $canViewVendor) {
                $data['vendor_id'] = $vendor->id;
            }
        }

        return $this->billRepo->save($data, $Bill);
    }

    public function convertQuote($quote)
    {
        $account = $quote->account;
        $Bill = $this->billRepo->cloneBill($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->billRepo->archive($quote);
        }

        return $Bill;
    }

    public function approveQuote($quote, BillInvitation $purchaseInvitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(BILL_TYPE_QUOTE) || $quote->quote_invoice_id) {
            return null;
        }

        event(new BillQuoteInvitationWasApproved($quote, $purchaseInvitation));

        if ($account->auto_convert_quote) {
            $Bill = $this->convertQuote($quote);

            foreach ($Bill->invitations as $BillInvitation) {
                if ($purchaseInvitation->contact_id == $BillInvitation->contact_id) {
                    $purchaseInvitation = $BillInvitation;
                }
            }
        } else {
            $quote->markApproved();
        }

        if ($account->auto_archive_quote) {
            $this->billRepo->archive($quote);
        }

        return $purchaseInvitation->invitation_key;
    }

    public function getDatatable($accountId, $vendorPublicId, $entityType, $search)
    {
        $datatable = new BillDatatable(true, true);
        $datatable->entityType = $entityType;

        $query = $this->billRepo
            ->getbills($accountId, $vendorPublicId, $entityType, $search)
            ->where('bills.bill_type_id', $entityType == ENTITY_BILL_QUOTE ? BILL_TYPE_QUOTE : BILL_TYPE_STANDARD);

        if (!Utils::hasPermission('view_bill')) {
            $query->where('bills.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
