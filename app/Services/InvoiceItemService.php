<?php

namespace App\Services;

use App\Events\QuoteInvitationWasApproved;
use App\Jobs\DownloadInvoices;
use App\Libraries\Utils;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\InvoiceItemRepository;
use Illuminate\Support\Facades\Auth;

class InvoiceItemService extends BaseService
{

    protected $invoiceItemRepo;
    protected $datatableService;

    /**
     *
     * InvoiceService constructor.
     *
     * @param InvoiceItemRepository $invoiceItemRepo
     * @param DatatableService $datatableService
     */
    public function __construct(InvoiceItemRepository $invoiceItemRepo, DatatableService $datatableService)
    {
        $this->invoiceItemRepo = $invoiceItemRepo;
        $this->datatableService = $datatableService;
    }

    public function bulk($ids, $action)
    {
        if ($action == 'download') {
            $invoice_items = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(Auth::user(), $invoice_items));

            return count($invoice_items);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    protected function getRepo()
    {
        return $this->invoiceItemRepo;
    }

    public function save(array $data, Invoice $invoice = null)
    {
        return $this->invoiceItemRepo->save($data, $invoice);
    }

    public function approveQuote($quote, Invitation $invitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_item_id) {
            return null;
        }

        event(new QuoteInvitationWasApproved($quote, $invitation));

        if ($account->auto_convert_quote) {
            $invoice = $this->convertQuote($quote);

            foreach ($invoice->invitations as $invoiceInvitation) {
                if ($invitation->contact_id == $invoiceInvitation->contact_id) {
                    $invitation = $invoiceInvitation;
                }
            }
        } else {
            $quote->markApproved();
        }

        if ($account->auto_archive_quote) {
            $this->invoiceItemRepo->archive($quote);
        }

        return $invitation->invitation_key;
    }

    public function convertQuote($quote)
    {
        $account = $quote->account;
        $invoice = $this->invoiceItemRepo->cloneInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->invoiceItemRepo->archive($quote);
        }

        return $invoice;
    }

    public function getDatatable($accountId, $invoiceItemPublicId, $entityType, $search)
    {
        $datatable = new InvoiceItemDatatable(true, $invoiceItemPublicId);
        $datatable->entityType = $entityType;

        $query = $this->invoiceItemRepo->getInvoices($accountId, $invoiceItemPublicId, $entityType, $search);

        if (!Utils::hasPermission('view_invoice_item')) {
            $query->where('invoice_items.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
