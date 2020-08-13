<?php

namespace App\Services;

use App\Events\billQuoteInvitationWasApproved;
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
    protected $BillRepo;
    protected $datatableService;

    /**
     *
     * BillService constructor.
     *
     * @param VendorRepository $vendorRepo
     * @param BillRepository $BillRepo
     * @param DatatableService $datatableService
     */
    public function __construct(
        VendorRepository $vendorRepo,
        BillRepository $BillRepo,
        DatatableService $datatableService)
    {
        $this->vendorRepo = $vendorRepo;
        $this->BillRepo = $BillRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->BillRepo;
    }

    public function bulk($ids, $action)
    {
        $user = Auth::user();
        if ($action == 'download') {
            $Bills = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadBill($user, $Bills));
            return count($Bills);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    public function save(array $data, Bill $Bill = null)
    {

        if (!empty($data['client'])) {
            $canSaveVendor = false;
            $canViewVendor = false;
            $vendorPublicId = array_get($data, 'client.public_id') ?: array_get($data, 'client.id');
            if (empty($vendorPublicId) || intval($vendorPublicId) < 0) {
                $canSaveVendor = Auth::user()->can('create', ENTITY_VENDOR);
            } else {
                $vendor = Vendor::scope($vendorPublicId)->first();
                $canSaveVendor = Auth::user()->can('edit', $vendor);
                $canViewVendor = Auth::user()->can('view', $vendor);
            }
//          if new vendor is created
            if ($canSaveVendor) {
                $vendor = $this->vendorRepo->save($data['client']);
            }
            if ($canSaveVendor || $canViewVendor) {
                $data['client_id'] = $vendor->id;
            }
        }

        return $this->BillRepo->save($data, $Bill);
    }

    public function convertQuote($quote)
    {
        $account = $quote->account;
        $Bill = $this->BillRepo->cloneBill($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->BillRepo->archive($quote);
        }

        return $Bill;
    }

    public function approveQuote($quote, BillInvitation $purchaseInvitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_id) {
            return null;
        }

        event(new billQuoteInvitationWasApproved($quote, $purchaseInvitation));

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
            $this->BillRepo->archive($quote);
        }

        return $purchaseInvitation->invitation_key;
    }

    public function getDatatable($accountId, $vendorPublicId, $entityType, $search)
    {
        $datatable = new BillDatatable(true, true);
        $datatable->entityType = $entityType;

        $query = $this->BillRepo
            ->getBills($accountId, $vendorPublicId, $entityType, $search)
            ->where('BILLs.invoice_type_id', $entityType == ENTITY_BILL_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD);

        if (!Utils::hasPermission('view_BILL')) {
            $query->where('BILLs.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
