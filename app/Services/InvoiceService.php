<?php

namespace App\Services;

use App\Events\QuoteInvitationWasApproved;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\Product;
use App\Ninja\Datatables\InvoiceDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Jobs\DownloadInvoices;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Utils;

class InvoiceService extends BaseService
{

    protected $clientRepo;
    protected $invoiceRepo;
    protected $datatableService;

    /**
     * InvoiceService constructor.
     *
     * @param ClientRepository $clientRepo
     * @param InvoiceRepository $invoiceRepo
     * @param DatatableService $datatableService
     */
    public function __construct(ClientRepository $clientRepo, InvoiceRepository $invoiceRepo, DatatableService $datatableService
    )
    {
        $this->clientRepo = $clientRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return InvoiceRepository
     */
    protected function getRepo()
    {
        return $this->invoiceRepo;
    }

    /**
     * @param $ids
     * @param $action
     *
     * @return int
     */
    public function bulk($ids, $action)
    {
        if ($action == 'download') {
            $invoices = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(Auth::user(), $invoices));
            return count($invoices);
        } else {
            return parent::bulk($ids, $action);
        }
    }

    /**
     * @param array $data
     * @param Invoice|null $invoice
     *
     * @return Invoice|Invoice|mixed
     */
    public function save(array $data, Invoice $invoice = null)
    {
//       adjust inventory
        if (!empty($data['invoice_items'])) {
            $this->inventoryAdjustment($data['invoice_items']);
        }
        if (!empty($data['client'])) {
            $canSaveClient = false;
            $canViewClient = false;
            $clientPublicId = array_get($data, 'client.public_id') ?: array_get($data, 'client.id');
            if (empty($clientPublicId) || intval($clientPublicId) < 0) {
                $canSaveClient = Auth::user()->can('create', ENTITY_CLIENT);
            } else {
                $client = Client::scope($clientPublicId)->first();
                $canSaveClient = Auth::user()->can('edit', $client);
                $canViewClient = Auth::user()->can('view', $client);
            }
            if ($canSaveClient) {
                $client = $this->clientRepo->save($data['client']);
            }
            if ($canSaveClient || $canViewClient) {
                $data['client_id'] = $client->id;
            }
        }
        return $this->invoiceRepo->save($data, $invoice);
    }

// stock adjustment
    public function inventoryAdjustment($items_data)
    {
        foreach ($items_data as $item_data) {
            if (!empty($item_data['qty'])) {
                if ((int)$item_data['qty'] > 0) {
                    $product = Product::scope($item_data['product_key'])->first();
                    if ($product) {
                        $qty = (int)$product->qty - (int)$item_data['qty'];
                        if ($qty > 0) {
                            $product->qty = (int)$product->qty - (int)$item_data['qty'];
                            $product->updated_by = auth::user()->username;
                            $product->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $quote
     * @return mixed
     */
    public function convertQuote($quote)
    {
        $account = $quote->account;
        $invoice = $this->invoiceRepo->cloneInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->invoiceRepo->archive($quote);
        }

        return $invoice;
    }

    /**
     * @param $quote
     * @param Invitation|null $invitation
     *
     * @return mixed|null
     */
    public function approveQuote($quote, Invitation $invitation = null)
    {
        $account = $quote->account;

        if (!$account->hasFeature(FEATURE_QUOTES) || !$quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_id) {
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
            $this->invoiceRepo->archive($quote);
        }

        return $invitation->invitation_key;
    }

    public function getDatatable($accountId, $clientPublicId, $entityType, $search)
    {
        $datatable = new InvoiceDatatable(true, $clientPublicId);
        $datatable->entityType = $entityType;

        $query = $this->invoiceRepo->getInvoices($accountId, $clientPublicId, $entityType, $search)
            ->where('invoices.invoice_type_id', '=', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD);

        if (!Utils::hasPermission('view_' . $entityType)) {
            $query->where('invoices.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
