<?php

namespace App\Services;

use App\Events\billQuoteInvitationWasApproved;
use App\Jobs\DownloadInvoices;
use App\Libraries\Utils;
use App\Models\BillInvitation;
use App\Models\InvoiceItem;
use App\Ninja\Datatables\BillItemDatatable;
use App\Ninja\Repositories\InvoiceItemRepository;
use Illuminate\Support\Facades\Auth;

class BillItemService extends BaseService
{

    protected $billItemRepo;
    protected $datatableService;

    /**
     *
     * InvoiceService constructor.
     *
     * @param InvoiceItemRepository $billItemRepo
     * @param DatatableService $datatableService
     */
    public function __construct(InvoiceItemRepository $billItemRepo, DatatableService $datatableService)
    {
        $this->billItemRepo = $billItemRepo;
        $this->datatableService = $datatableService;
    }

    public function bulk($ids, $action)
    {
        if ($action == 'download') {
            $billItems = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(Auth::user(), $billItems));

            return count($billItems);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    protected function getRepo()
    {
        return $this->billItemRepo;
    }

    public function save(array $data, InvoiceItem $billItem = null)
    {
        return $this->billItemRepo->save($data, $billItem);
    }

    public function approveQuote($quote, BillInvitation $invitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(BILL_TYPE_QUOTE) || $quote->quote_bill_item_id) {
            return null;
        }

        event(new billQuoteInvitationWasApproved($quote, $invitation));

        if ($account->auto_convert_quote) {
            $billItem = $this->convertQuote($quote);

            foreach ($billItem->invitations as $billItemPurchaseInvitation) {
                if ($invitation->contact_id == $billItemPurchaseInvitation->contact_id) {
                    $invitation = $billItemPurchaseInvitation;
                }
            }
        } else {
            $quote->markApproved();
        }

        if ($account->auto_archive_quote) {
            $this->billItemRepo->archive($quote);
        }

        return $invitation->invitation_key;
    }

    public function convertQuote($quote)
    {
        $account = $quote->account;
        $billItem = $this->billItemRepo->cloneInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->billItemRepo->archive($quote);
        }

        return $billItem;
    }

    public function getDatatable($accountId, $search)
    {
        $datatable = new BillItemDatatable(true);

        $query = $this->billItemRepo->find($accountId, $search);

        if (!Utils::hasPermission('view_bill_item')) {
            $query->where('bill_items.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
