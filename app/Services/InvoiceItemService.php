<?php

namespace App\Services;

use App\Events\QuoteInvitationWasApprovedEvent;
use App\Jobs\DownloadInvoices;
use App\Libraries\Utils;
use App\Models\Invitation;
use App\Models\InvoiceItem;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\InvoiceItemRepository;
use Illuminate\Support\Facades\Auth;

class InvoiceItemService extends BaseService
{

    protected $invoiceItemItemRepo;
    protected $datatableService;

    /**
     *
     * InvoiceService constructor.
     *
     * @param InvoiceItemRepository $invoiceItemItemRepo
     * @param DatatableService $datatableService
     */
    public function __construct(InvoiceItemRepository $invoiceItemItemRepo, DatatableService $datatableService)
    {
        $this->invoiceItemRepo = $invoiceItemItemRepo;
        $this->datatableService = $datatableService;
    }

    public function bulk($ids, $action)
    {
        if ($action == 'download') {
            $invoiceItem_items = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(Auth::user(), $invoiceItem_items));

            return count($invoiceItem_items);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    protected function getRepo()
    {
        return $this->invoiceItemRepo;
    }

    public function save(array $data, InvoiceItem $invoiceItem = null)
    {
        return $this->invoiceItemRepo->save($data, $invoiceItem);
    }

    public function approveQuote($quote, Invitation $invitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_item_id) {
            return null;
        }

        event(new QuoteInvitationWasApprovedEvent($quote, $invitation));

        if ($account->auto_convert_quote) {
            $invoiceItem = $this->convertQuote($quote);

            foreach ($invoiceItem->invitations as $invoiceItemInvitation) {
                if ($invitation->contact_id == $invoiceItemInvitation->contact_id) {
                    $invitation = $invoiceItemInvitation;
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
        $invoiceItem = $this->invoiceItemRepo->cloneInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->invoiceItemRepo->archive($quote);
        }

        return $invoiceItem;
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new InvoiceItemDatatable(true);

        $query = $this->invoiceItemRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_invoice_item')) {
            $query->where('invoice_items.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
