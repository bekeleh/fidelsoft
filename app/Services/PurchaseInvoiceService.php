<?php

namespace App\Services;

use App\Events\purchaseQuoteInvitationWasApproved;
use App\Jobs\DownloadPurchaseInvoice;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\PurchaseInvitation;
use App\Models\PurchaseInvoice;
use App\Ninja\Datatables\PurchaseInvoiceDatatable;
use App\Ninja\Repositories\VendorRepository;
use App\Ninja\Repositories\PurchaseInvoiceRepository;
use Illuminate\Support\Facades\Auth;

class PurchaseInvoiceService extends BaseService
{

    protected $vendorRepo;
    protected $purchaseInvoiceRepo;
    protected $datatableService;

    /**
     *
     * PurchaseInvoiceService constructor.
     *
     * @param VendorRepository $vendorRepo
     * @param PurchaseInvoiceRepository $purchaseInvoiceRepo
     * @param DatatableService $datatableService
     */
    public function __construct(
        VendorRepository $vendorRepo,
        PurchaseInvoiceRepository $purchaseInvoiceRepo,
        DatatableService $datatableService)
    {
        $this->vendorRepo = $vendorRepo;
        $this->purchaseInvoiceRepo = $purchaseInvoiceRepo;
        $this->datatableService = $datatableService;
    }


    protected function getRepo()
    {
        return $this->purchaseInvoiceRepo;
    }

    public function bulk($ids, $action)
    {
        $user = Auth::user();
        if ($action == 'download') {
            $purchaseInvoices = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadPurchaseInvoice($user, $purchaseInvoices));
            return count($purchaseInvoices);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    public function save(array $data, PurchaseInvoice $purchaseInvoice = null)
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
            if ($canSaveVendor) {
                $vendor = $this->vendorRepo->save($data['vendor']);
            }
            if ($canSaveVendor || $canViewVendor) {
                $data['vendor_id'] = $vendor->id;
            }
        }

        return $this->purchaseInvoiceRepo->save($data, $purchaseInvoice);
    }

    public function convertQuote($quote)
    {
        $account = $quote->account;
        $purchaseInvoice = $this->purchaseInvoiceRepo->clonePurchaseInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->purchaseInvoiceRepo->archive($quote);
        }

        return $purchaseInvoice;
    }

    public function approveQuote($quote, PurchaseInvitation $purchaseInvitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(PURCHASE_INVOICE_TYPE_QUOTE) || $quote->quote_invoice_id) {
            return null;
        }

        event(new purchaseQuoteInvitationWasApproved($quote, $purchaseInvitation));

        if ($account->auto_convert_quote) {
            $purchaseInvoice = $this->convertQuote($quote);

            foreach ($purchaseInvoice->purchase_invitations as $purchaseInvoiceInvitation) {
                if ($purchaseInvitation->vendor_contact_id == $purchaseInvoiceInvitation->vendor_contact_id) {
                    $purchaseInvitation = $purchaseInvoiceInvitation;
                }
            }
        } else {
            $quote->markApproved();
        }

        if ($account->auto_archive_quote) {
            $this->purchaseInvoiceRepo->archive($quote);
        }

        return $purchaseInvitation->invitation_key;
    }

    public function getDatatable($accountId, $vendorPublicId, $entityType, $search)
    {
        $datatable = new PurchaseInvoiceDatatable(true, true);
        $datatable->entityType = $entityType;

        $query = $this->purchaseInvoiceRepo
            ->getPurchaseInvoices($accountId, $vendorPublicId, $entityType, $search)
            ->where('purchase_invoices.invoice_type_id', $entityType == ENTITY_PURCHASE_QUOTE ? PURCHASE_INVOICE_TYPE_QUOTE : PURCHASE_INVOICE_TYPE_STANDARD);

        if (!Utils::hasPermission('view_purchase_invoice')) {
            $query->where('purchase_invoices.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
