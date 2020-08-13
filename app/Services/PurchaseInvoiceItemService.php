<?php

namespace App\Services;

use App\Events\billQuoteInvitationWasApproved;
use App\Jobs\DownloadInvoices;
use App\Libraries\Utils;
use App\Models\BillInvitation;
use App\Models\InvoiceItem;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\InvoiceItemRepository;
use Illuminate\Support\Facades\Auth;

class BillItemService extends BaseService
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

    public function approveQuote($quote, BillInvitation $invitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_item_id) {
            return null;
        }

        event(new billQuoteInvitationWasApproved($quote, $invitation));

        if ($account->auto_convert_quote) {
            $invoiceItem = $this->convertQuote($quote);

            foreach ($invoiceItem->invitations as $invoiceItemPurchaseInvitation) {
                if ($invitation->contact_id == $invoiceItemPurchaseInvitation->contact_id) {
                    $invitation = $invoiceItemPurchaseInvitation;
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
