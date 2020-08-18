<?php

namespace App\Ninja\Repositories;

use App\Events\Sale\InvoiceItemsWereCreatedEvent;
use App\Events\Sale\InvoiceItemsWereUpdatedEvent;
use App\Events\Sale\QuoteItemsWereCreatedEvent;
use App\Events\Sale\QuoteItemsWereUpdatedEvent;
use App\Jobs\SendInvoiceEmail;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Common\Account;
use App\Models\EntityModel;
use App\Models\Document;
use App\Models\Expense;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ItemStore;
use App\Models\Task;
use App\Services\PaymentService;
use Datatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceRepository extends BaseRepository
{
    protected $documentRepo;
    protected $model;
    protected $paymentService;
    protected $paymentRepo;

    public function __construct(
        Invoice $model,
        PaymentService $paymentService,
        PaymentRepository $paymentRepo,
        DocumentRepository $documentRepo)
    {
        $this->model = $model;
        $this->paymentService = $paymentService;
        $this->paymentRepo = $paymentRepo;
        $this->documentRepo = $documentRepo;
    }

    public function getClassName()
    {
        return 'App\Models\Invoice';
    }

    public function all()
    {
        return Invoice::scope()
            ->invoiceType(INVOICE_TYPE_STANDARD)
            ->with('user', 'client.contacts', 'invoice_status')
            ->withTrashed()->where('is_recurring', false)->get();
    }

    /**
     * @param bool $accountId
     * @param bool $clientPublicId
     * @param string $entityType
     * @param bool $filter
     * @return mixed|null
     */
    public function getInvoices($accountId = false, $clientPublicId = false, $entityType = null, $filter = false)
    {

        $query = DB::table('invoices')
            ->LeftJoin('accounts', 'accounts.id', 'invoices.account_id')
            ->LeftJoin('clients', 'clients.id', 'invoices.client_id')
            ->leftJoin('invoice_statuses', 'invoice_statuses.id', 'invoices.invoice_status_id')
            ->LeftJoin('contacts', 'contacts.client_id', 'clients.id')
            ->LeftJoin('branches', 'branches.id', 'invoices.branch_id')
            ->where('invoices.account_id', $accountId)
            ->where('contacts.deleted_at', null)
            ->where('invoices.is_recurring', false)
            ->where('contacts.is_primary', true)
//->whereRaw('(clients.name != "" or contacts.first_name != "" or contacts.last_name != "" or contacts.email != "")') // filter out buy now invoices
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                'clients.public_id as client_public_id',
                'clients.user_id as client_user_id',
                'invoices.invoice_number',
                'invoices.invoice_number as quote_number',
                'invoices.invoice_status_id',
                DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'invoices.public_id',
                'invoices.amount',
                'invoices.balance',
                'invoices.discount',
                'invoices.invoice_date',
                'invoices.due_date as due_date_sql',
                'invoices.partial_due_date',
                DB::raw("CONCAT(invoices.invoice_date, invoices.created_at) as date"),
                DB::raw("CONCAT(COALESCE(invoices.partial_due_date, invoices.due_date), invoices.created_at) as due_date"),
                DB::raw("CONCAT(COALESCE(invoices.partial_due_date, invoices.due_date), invoices.created_at) as valid_until"),
                'invoice_statuses.name as status',
                'invoice_statuses.name as invoice_status_name',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.email',
                'invoices.quote_id',
                'invoices.quote_invoice_id',
                'invoices.deleted_at',
                'invoices.is_deleted',
                'invoices.partial',
                'invoices.user_id',
                'invoices.is_public',
                'invoices.is_recurring',
                'invoices.private_notes',
                'invoices.public_notes',
                'invoices.created_at',
                'invoices.updated_at',
                'invoices.deleted_at',
                'invoices.created_by',
                'invoices.updated_by',
                'invoices.deleted_by',
                'branches.public_id as branch_public_id',
                'branches.name as branch_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('invoices.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('invoice_statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('branches.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, $entityType, ENTITY_INVOICE);

        if ($statuses = session('entity_status_filter:' . $entityType)) {
            $statuses = explode(',', $statuses);
            $query->where(function ($query) use ($statuses) {
                foreach ($statuses as $status) {
                    if (in_array($status, EntityModel::$statuses)) {
                        continue;
                    }
                    $query->orWhere('invoice_status_id', $status);
                }
                if (in_array(INVOICE_STATUS_UNPAID, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('invoices.balance', '>', 0)
                            ->where('invoices.is_public', true);
                    });
                }
                if (in_array(INVOICE_STATUS_OVERDUE, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('invoices.balance', '>', 0)
                            ->where('invoices.due_date', '<', date('Y-m-d'))
                            ->where('invoices.is_public', true);
                    });
                }
            });
        }

        if ($clientPublicId) {
            $query->where('clients.public_id', $clientPublicId);
        } else {
            $query->where('clients.deleted_at', null);
        }

        return $query;
    }

    /**
     * @param bool $accountId
     * @param bool $clientPublicId
     * @param null $entityType
     * @param bool $filter
     * @return mixed
     */
    public function getRecurringInvoices($accountId = false, $clientPublicId = false, $entityType = null, $filter = false)
    {
        $query = DB::table('invoices')
            ->leftJoin('accounts', 'accounts.id', 'invoices.account_id')
            ->leftJoin('clients', 'clients.id', 'invoices.client_id')
            ->leftJoin('invoice_statuses', 'invoice_statuses.id', 'invoices.invoice_status_id')
            ->leftJoin('frequencies', 'frequencies.id', 'invoices.frequency_id')
            ->leftJoin('contacts', 'contacts.client_id', 'clients.id')
            ->where('invoices.account_id', $accountId)
            ->where('invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('contacts.deleted_at', null)
            ->where('invoices.is_recurring', true)
            ->where('contacts.is_primary', true)
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                'clients.public_id as client_public_id',
                DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'invoices.public_id',
                'invoices.amount',
                'frequencies.name as frequency',
                'invoices.start_date as start_date_sql',
                'invoices.end_date as end_date_sql',
                'invoices.last_sent_date as last_sent_date_sql',
                DB::raw("CONCAT(invoices.start_date, invoices.created_at) as start_date"),
                DB::raw("CONCAT(invoices.end_date, invoices.created_at) as end_date"),
                DB::raw("CONCAT(invoices.last_sent_date, invoices.created_at) as last_sent"),
                'contacts.first_name',
                'contacts.last_name',
                'contacts.email',
                'invoices.deleted_at',
                'invoices.is_deleted',
                'invoices.user_id',
                'invoice_statuses.name as invoice_status_name',
                'invoice_statuses.name as status',
                'invoices.invoice_status_id',
                'invoices.balance',
                'invoices.due_date',
                'invoices.due_date as due_date_sql',
                'invoices.is_recurring',
                'invoices.quote_invoice_id',
                'invoices.public_notes',
                'invoices.private_notes',
                'invoices.created_at',
                'invoices.updated_at',
                'invoices.deleted_at',
                'invoices.created_by',
                'invoices.updated_by',
                'invoices.deleted_by'
            );

        if ($clientPublicId) {
            $query->where('clients.public_id', $clientPublicId);
        } else {
            $query->where('clients.deleted_at', null);
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('invoices.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, $entityType, ENTITY_INVOICE);

        return $query;
    }

    /**
     * @param $contactId
     * @param null $filter
     * @return mixed
     */
    public function getClientRecurringDatatable($contactId, $filter = null)
    {
        $query = DB::table('invitations')
            ->leftJoin('accounts', 'accounts.id', 'invitations.account_id')
            ->leftJoin('invoices', 'invoices.id', 'invitations.invoice_id')
            ->leftJoin('clients', 'clients.id', 'invoices.client_id')
            ->leftJoin('frequencies', 'frequencies.id', 'invoices.frequency_id')
            ->where('invitations.contact_id', $contactId)
            ->where('invitations.deleted_at', null)
            ->where('invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('invoices.is_deleted', false)
            ->where('clients.deleted_at', null)
            ->where('invoices.is_recurring', true)
            ->where('invoices.is_public', true)
            ->where('invoices.deleted_at', null)
//->where('invoices.start_date', '>=', date('Y-m-d H:i:s'))
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                'invitations.invitation_key',
                'invoices.invoice_number',
                'invoices.due_date',
                'clients.public_id as client_public_id',
                'clients.name as client_name',
                'invoices.public_id',
                'invoices.amount',
                'invoices.start_date',
                'invoices.end_date',
                'invoices.auto_bill',
                'invoices.client_enable_auto_bill',
                'frequencies.name as frequency',
                'invoices.created_at',
                'invoices.updated_at',
                'invoices.deleted_at',
                'invoices.created_by',
                'invoices.updated_by',
                'invoices.deleted_by'
            );

        $table = Datatable::query($query)
            ->addColumn('frequency', function ($model) {
                $frequency = strtolower($model->frequency);
                $frequency = preg_replace('/\s/', '_', $frequency);
                return trans('texts.freq_' . $frequency);
            })
            ->addColumn('start_date', function ($model) {
                return Utils::fromSqlDate($model->start_date);
            })
            ->addColumn('end_date', function ($model) {
                return Utils::fromSqlDate($model->end_date);
            })
            ->addColumn('amount', function ($model) {
                return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
            })
            ->addColumn('client_enable_auto_bill', function ($model) {
                if ($model->auto_bill == AUTO_BILL_OFF) {
                    return trans('texts.disabled');
                } elseif ($model->auto_bill == AUTO_BILL_ALWAYS) {
                    return trans('texts.enabled');
                } elseif ($model->client_enable_auto_bill) {
                    return trans('texts.enabled') . ' - <a href="javascript:setAutoBill(' . $model->public_id . ',false)">' . trans('texts.disable') . '</a>';
                } else {
                    return trans('texts.disabled') . ' - <a href="javascript:setAutoBill(' . $model->public_id . ',true)">' . trans('texts.enable') . '</a>';
                }
            });

        return $table->make();
    }

    /**
     * @param $contactId
     * @param $entityType
     * @param $search
     * @return mixed
     */
    public function getClientDatatable($contactId, $entityType, $search)
    {
        $query = DB::table('invitations')
            ->leftJoin('accounts', 'accounts.id', 'invitations.account_id')
            ->leftJoin('invoices', 'invoices.id', 'invitations.invoice_id')
            ->leftJoin('clients', 'clients.id', 'invoices.client_id')
            ->leftJoin('contacts', 'contacts.client_id', 'clients.id')
            ->where('invitations.contact_id', $contactId)
            ->where('invitations.deleted_at', null)
            ->where('invoices.invoice_type_id', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD)
            ->where('invoices.is_deleted', false)
            ->where('clients.deleted_at', null)
            ->where('contacts.deleted_at', null)
            ->where('contacts.is_primary', true)
            ->where('invoices.is_recurring', false)
            ->where('invoices.is_public', true)
// Only show paid invoices for ninja accounts
//            ->whereRaw(sprintf("((accounts.account_key != '%s' and accounts.account_key not like '%s%%') or invoices.invoice_status_id = %d)", env('NINJA_LICENSE_ACCOUNT_KEY'), substr(NINJA_ACCOUNT_KEY, 0, 30), INVOICE_STATUS_PAID))
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                'invitations.invitation_key',
                'invoices.invoice_number',
                'invoices.invoice_date',
                'invoices.balance as balance',
                'invoices.due_date',
                'invoices.invoice_status_id',
                'invoices.due_date',
                'invoices.quote_invoice_id',
                'clients.public_id as client_public_id',
                DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'invoices.public_id',
                'invoices.amount',
                'invoices.start_date',
                'invoices.end_date',
                'invoices.partial',
                'invoices.created_at',
                'invoices.updated_at',
                'invoices.deleted_at',
                'invoices.created_by',
                'invoices.updated_by',
                'invoices.deleted_by'
            );

        $table = Datatable::query($query)
            ->addColumn('invoice_number', function ($model) use ($entityType) {
                return link_to('/view/' . $model->invitation_key, $model->invoice_number)->toHtml();
            })
            ->addColumn('invoice_date', function ($model) {
                return Utils::fromSqlDate($model->invoice_date);
            })
            ->addColumn('amount', function ($model) {
                return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
            });

        if ($entityType == ENTITY_INVOICE) {
            $table->addColumn('balance', function ($model) {
                return $model->partial > 0 ?
                    trans('texts.partial_remaining', [
                        'partial' => Utils::formatMoney($model->partial, $model->currency_id, $model->country_id),
                        'balance' => Utils::formatMoney($model->balance, $model->currency_id, $model->country_id),
                    ]) :
                    Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
            });
        }

        return $table->addColumn('due_date', function ($model) {
            return Utils::fromSqlDate($model->due_date);
        })
            ->addColumn('invoice_status_id', function ($model) use ($entityType) {
                if ($model->invoice_status_id == INVOICE_STATUS_PAID) {
                    $label = trans('texts.status_paid');
                    $class = 'success';
                } elseif ($model->invoice_status_id == INVOICE_STATUS_PARTIAL) {
                    $label = trans('texts.status_partial');
                    $class = 'info';
                } elseif ($entityType == ENTITY_QUOTE && ($model->invoice_status_id >= INVOICE_STATUS_APPROVED || $model->quote_invoice_id)) {
                    $label = trans('texts.status_approved');
                    $class = 'success';
                } elseif (Invoice::calcIsOverdue($model->balance, $model->due_date)) {
                    $class = 'danger';
                    if ($entityType == ENTITY_INVOICE) {
                        $label = trans('texts.past_due');
                    } else {
                        $label = trans('texts.expired');
                    }
                } else {
                    $class = 'default';
                    if ($entityType == ENTITY_INVOICE) {
                        $label = trans('texts.unpaid');
                    } else {
                        $label = trans('texts.pending');
                    }
                }

                return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
            })->make();
    }

    /**
     * @param array $data
     * @param Invoice|null $invoice
     * @return Invoice
     */
    public function save(array $data, Invoice $invoice = null)
    {
        $account = $invoice ? $invoice->account : Auth::user()->account;
        $publicId = !empty($data['public_id']) ? $data['public_id'] : false;
        $isNew = !$publicId || intval($publicId) < 0;

        if ($invoice) {
            $entityType = $invoice->getEntityType();
            $invoice->updated_by = Auth::user()->username;
        } elseif ($isNew) {
            $entityType = ENTITY_INVOICE;
            if (!empty($data['is_recurring']) && filter_var($data['is_recurring'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_RECURRING_INVOICE;
            } elseif (!empty($data['is_quote']) && filter_var($data['is_quote'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_QUOTE;
            }
            $invoice = $account->createInvoice($entityType, $data['client_id']);
            $invoice->invoice_date = date_create()->format('Y-m-d');
            $invoice->custom_taxes1 = $account->custom_invoice_taxes1 ?: false;
            $invoice->custom_taxes2 = $account->custom_invoice_taxes2 ?: false;
            $invoice->created_by = Auth::user()->username;
            $invoice->branch_id = Auth::user()->branch->id;
            // set the default due date
            if ($entityType == ENTITY_INVOICE && empty($data['partial_due_date'])) {
                $client = Client::scope()->where('id', $data['client_id'])->first();
                $invoice->due_date = $account->defaultDueDate($client);
            }
        } else {
            $invoice = Invoice::scope($publicId)->firstOrFail();
        }
        if (!empty($invoice->is_deleted)) {
            return $invoice;
        } elseif ($invoice->isLocked()) {
            return $invoice;
        }

        if (!empty($data['has_tasks']) && filter_var($data['has_tasks'], FILTER_VALIDATE_BOOLEAN)) {
            $invoice->has_tasks = true;
        }
        if (!empty($data['has_expenses']) && filter_var($data['has_expenses'], FILTER_VALIDATE_BOOLEAN)) {
            $invoice->has_expenses = true;
        }

        if (!empty($data['is_public']) && filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN)) {
            $invoice->is_public = true;
            if (!$invoice->isSent()) {
                $invoice->invoice_status_id = INVOICE_STATUS_SENT;
            }
        }

//     TODO: should be examine this expression
        if (!empty($data['invoice_design_id']) && !$data['invoice_design_id']) {
            $data['invoice_design_id'] = 1;
        }

//      fill invoice
        $invoice->fill($data);

//      update account default template
        $this->saveAccountDefault($account, $invoice, $data);

        if (!empty($data['invoice_number']) && !empty($invoice->is_recurring)) {
            $invoice->invoice_number = trim($data['invoice_number']);
        }

        if (!empty($data['discount'])) {
            $invoice->discount = round(Utils::parseFloat($data['discount']), 2);
        }
        if (!empty($data['is_amount_discount'])) {
            $invoice->is_amount_discount = $data['is_amount_discount'] ? true : false;
        }

        if (!empty($data['invoice_date_sql'])) {
            $invoice->invoice_date = $data['invoice_date_sql'];
        } elseif (!empty($data['invoice_date'])) {
            $invoice->invoice_date = Utils::toSqlDate($data['invoice_date']);
        }

        if (!empty($data['invoice_status_id'])) {
            if ($data['invoice_status_id'] == 0) {
                $data['invoice_status_id'] = INVOICE_STATUS_DRAFT;
            }
            $invoice->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        } else {
            $invoice->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        }

        if ($invoice->is_recurring) {
            if (!$isNew && !empty($data['start_date']) && $invoice->start_date && $invoice->start_date != Utils::toSqlDate($data['start_date'])) {
                $invoice->last_sent_date = null;
            }

            $invoice->frequency_id = array_get($data, 'frequency_id', FREQUENCY_MONTHLY);
            $invoice->start_date = Utils::toSqlDate(array_get($data, 'start_date'));
            $invoice->end_date = Utils::toSqlDate(array_get($data, 'end_date'));
            $invoice->client_enable_auto_bill = !empty($data['client_enable_auto_bill']) && $data['client_enable_auto_bill'] ? true : false;
            $invoice->auto_bill = array_get($data, 'auto_bill_id') ?: array_get($data, 'auto_bill', AUTO_BILL_OFF);

            if ($invoice->auto_bill < AUTO_BILL_OFF || $invoice->auto_bill > AUTO_BILL_ALWAYS) {
                $invoice->auto_bill = AUTO_BILL_OFF;
            }

            if (!empty($data['recurring_due_date'])) {
                $invoice->due_date = $data['recurring_due_date'];
            } elseif (!empty($data['due_date'])) {
                $invoice->due_date = $data['due_date'];
            }
        } else {
            if ($isNew && empty($data['due_date']) && empty($data['due_date_sql'])) {
                //
            } elseif (!empty($data['due_date']) || !empty($data['due_date_sql'])) {
                $invoice->due_date = !empty($data['due_date_sql']) ? $data['due_date_sql'] :
                    Utils::toSqlDate($data['due_date']);
            }
//         invoice is not recurring
            $invoice->frequency_id = 0;
            $invoice->start_date = null;
            $invoice->end_date = null;
        }

        if (!empty($data['terms'])) {
            $invoice->terms = trim($data['terms']);
        } elseif ($isNew && !empty($invoice->is_recurring) && $account->{"{$entityType}_terms"}) {
            $invoice->terms = $account->{"{$entityType}_terms"};
        } else {
            $invoice->terms = '';
        }

        if (!empty($data['invoice_footer'])) {
            $invoice->invoice_footer = trim($data['invoice_footer']);
        } elseif ($isNew && !empty($invoice->is_recurring) && !empty($account->invoice_footer)) {
            $invoice->invoice_footer = $account->invoice_footer;
        } else {
            $invoice->invoice_footer = '';
        }

        $invoice->public_notes = !empty($data['public_notes']) ? trim($data['public_notes']) : '';

// process date variables if not recurring
        if (!empty($invoice->is_recurring)) {
            $invoice->terms = Utils::processVariables($invoice->terms);
            $invoice->invoice_footer = Utils::processVariables($invoice->invoice_footer);
            $invoice->public_notes = Utils::processVariables($invoice->public_notes);
        }

        if (!empty($data['po_number'])) {
            $invoice->po_number = trim($data['po_number']);
        }

//    provide backwards compatibility
        if (!empty($data['tax_name']) && !empty($data['tax_rate'])) {
            $data['tax_name1'] = $data['tax_name'];
            $data['tax_rate1'] = $data['tax_rate'];
        }

//       line item total
        $total = 0;
        $total = $this->getLineItemNetTotal($account, $invoice, $data);

//      line item tax
        $itemTax = 0;
        $itemTax = $this->getLineItemNetTax($account, $invoice, $data, $total);

//       save sale invoice detail
        $this->saveSaleInvoiceDetail($account, $invoice, $data, $total, $itemTax, $publicId);

        $origLineItems = [];
        if (!empty($publicId)) {
            $origLineItems = !empty($invoice->invoice_items) ?
                $invoice->invoice_items()->get()->toArray() : '';
//            remove old invoice line items
            $invoice->invoice_items()->forceDelete();
        }
//      update if any invoice documents
        if (!empty($data['document_ids'])) {
            $document_ids = array_map('intval', $data['document_ids']);
            $this->saveInvoiceDocuments($invoice, $document_ids);
            $this->updateInvoiceDocuments($invoice, $document_ids);

        }

//      sales invoice line item detail
        $this->saveLineItemDetail($account, $invoice, $data, $origLineItems, $isNew);

        $this->saveInvitations($invoice);

//      finally dispatch events
        $this->dispatchEvents($invoice);

        return $invoice->load('invoice_items');
    }

    private function saveInvitations($invoice)
    {
        if (empty($invoice)) {
            return null;
        }
        $client = $invoice->client;

        $client->load('contacts');
        $sendInvoiceIds = [];

        if (!$client->contacts->count()) {
            return $invoice;
        }

//      those who have send invoice flag client contact
        foreach ($client->contacts as $contact) {
            if ($contact->send_invoice) {
                $sendInvoiceIds[] = $contact->id;
            }
        }

        // if no contacts are selected auto-select the first to ensure there's an invitation
        if (!count($sendInvoiceIds)) {
            $sendInvoiceIds[] = $client->contacts[0]->id;
        }

        foreach ($client->contacts as $contact) {
            $invitation = Invitation::scope()->where('contact_id', $contact->id)
                ->where('invoice_id', $invoice->id)->first();
            if (in_array($contact->id, $sendInvoiceIds) && empty($invitation)) {
                $invitation = Invitation::createNew($invoice);
                $invitation->invoice_id = $invoice->id;
                $invitation->contact_id = $contact->id;
                $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                $invitation->save();
            } elseif (!in_array($contact->id, $sendInvoiceIds) && !empty($invitation)) {
                $invitation->delete();
            }
        }

        if ($invoice->is_public && !$invoice->areInvitationsSent()) {
            $invoice->markInvitationsSent();
        }

        return $invoice;
    }

    /**
     * @param $invoice
     * @return null
     */
    private function dispatchEvents($invoice)
    {
        if (empty($invoice)) {
            return null;
        }
        if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
            if ($invoice->wasRecentlyCreated) {
                event(new QuoteItemsWereCreatedEvent($invoice));
            } else {
                event(new QuoteItemsWereUpdatedEvent($invoice));
            }
        } else {
            if ($invoice->wasRecentlyCreated) {
                event(new InvoiceItemsWereCreatedEvent($invoice));
            } else {
                event(new InvoiceItemsWereUpdatedEvent($invoice));
            }
        }
    }

    /**
     * @param Invoice $invoice
     * @param null $quoteId
     * @return mixed|null
     */
    public function cloneInvoice(Invoice $invoice, $quoteId = null)
    {
        if (empty($invoice)) {
            return null;
        }

        $invoice->load('invitations', 'invoice_items');
        $account = $invoice->account;

        $clone = Invoice::createNew($invoice);
        $clone->balance = $invoice->amount;

// if the invoice prefix is diff than quote prefix, use the same number for the invoice (if it's available)
        $invoiceNumber = false;
        if ($account->hasInvoicePrefix() && $account->share_counter) {
            $invoiceNumber = $invoice->invoice_number;
            if ($account->quote_number_prefix && strpos($invoiceNumber, $account->quote_number_prefix) === 0) {
                $invoiceNumber = substr($invoiceNumber, strlen($account->quote_number_prefix));
            }
            $invoiceNumber = $account->invoice_number_prefix . $invoiceNumber;
            $invoice = Invoice::scope(false, $account->id)
                ->withTrashed()
                ->where('invoice_number', $invoiceNumber)
                ->first();
            if ($invoice) {
                $invoiceNumber = false;
            } else {
// since we aren't using the counter we need to offset it by one
                $account->invoice_number_counter -= 1;
                $account->save();
            }
        }

        foreach ([
                     'client_id',
                     'discount',
                     'is_amount_discount',
                     'po_number',
                     'is_recurring',
                     'frequency_id',
                     'start_date',
                     'end_date',
                     'terms',
                     'invoice_footer',
                     'public_notes',
                     'invoice_design_id',
                     'tax_name1',
                     'tax_rate1',
                     'tax_name2',
                     'tax_rate2',
                     'amount',
                     'invoice_type_id',
                     'custom_value1',
                     'custom_value2',
                     'custom_taxes1',
                     'custom_taxes2',
                     'partial',
                     'custom_text_value1',
                     'custom_text_value2',
                 ] as $field) {
            $clone->$field = $invoice->$field;
        }

        if ($quoteId) {
            $clone->invoice_type_id = INVOICE_TYPE_STANDARD;
            $clone->quote_id = $quoteId;
            if ($account->invoice_terms) {
                $clone->terms = $account->invoice_terms;
            }
            if (!auth()->check()) {
                $clone->is_public = true;
                $clone->invoice_status_id = INVOICE_STATUS_SENT;
            }
        }

        $clone->invoice_number = $invoiceNumber ?: $account->getClientNextNumber($clone);
        $clone->invoice_date = date_create()->format('Y-m-d');
        $clone->due_date = $account->defaultDueDate($invoice->client);
        $clone->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
        $clone->save();

        if ($quoteId) {
            $invoice->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
            $invoice->quote_invoice_id = $clone->public_id;
            $invoice->save();
        }

        foreach ($invoice->invoice_items as $item) {
//          invoice item instance
            $cloneItem = InvoiceItem::createNew($invoice);
            foreach ([
                         'product_id',
                         'product_key',
                         'notes',
                         'cost',
                         'qty',
                         'tax_name1',
                         'tax_rate1',
                         'tax_name2',
                         'tax_rate2',
                         'custom_value1',
                         'custom_value2',
                         'discount',
                     ] as $field) {

                $cloneItem->$field = $item->$field;
            }

            $product = $this->getProductDetail($account, $item->product_key);
            if (!empty($product)) {
                $itemStore = $this->getItemStore($account, $product);
                $qoh = $itemStore->qty;
                $this->updateItemStore($qoh, $cloneItem->qty, $itemStore);
            }

            $clone->invoice_items()->save($cloneItem);
        }

        foreach ($invoice->documents as $document) {
            $cloneDocument = $document->cloneDocument();
            $clone->documents()->save($cloneDocument);
        }

        foreach ($invoice->invitations as $invitation) {
            $cloneInvitation = Invitation::createNew($invoice);
            $cloneInvitation->contact_id = $invitation->contact_id;
            $cloneInvitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $clone->invitations()->save($cloneInvitation);
        }

        $this->dispatchEvents($clone);

        return $clone;
    }

    /**
     * @param Invoice $invoice
     * @return mixed|null
     */
    public function emailInvoice(Invoice $invoice)
    {
        if (empty($invoice)) {
            return null;
        }

        if (config('queue.default') === 'sync') {
            app('App\Ninja\Mailers\ContactMailer')->sendInvoice($invoice);
        } else {
            dispatch(new SendInvoiceEmail($invoice));
        }
    }

    /**
     * @param Invoice $invoice
     */
    public function markSent(Invoice $invoice)
    {
        $invoice->markSent();
    }

    /**
     * @param Invoice $invoice
     * @return mixed|void|null
     */
    public function markPaid(Invoice $invoice)
    {
        if (!$invoice->canBePaid()) {
            return null;
        }

        $invoice->markSentIfUnsent();

        $data = [
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'amount' => $invoice->balance,
        ];

        return $this->paymentRepo->save($data);
    }

    /**
     * @param $invitationKey
     * @return Invitation|mixed
     */
    public function findInvoiceByInvitation($invitationKey)
    {
        if (empty($invitationKey)) {
            return false;
        }
// check for extra params at end of value (from website feature)
        list($invitationKey) = explode('&', $invitationKey);
        $invitationKey = substr($invitationKey, 0, RANDOM_KEY_LENGTH);

        /** @var Invitation $invitation */
        $invitation = Invitation::with('invoice')
            ->where('invitation_key', $invitationKey)->first();

        if (empty($invitation)) {
            return false;
        }

        $invoice = $invitation->invoice
            ->load('user', 'invoice_items', 'invoice_design', 'documents', 'account.country', 'client.contacts', 'client.country');

        if (empty($invoice) || !empty($invoice->is_deleted)) {
            return false;
        }

        $client = $invoice->client;

        if (empty($client) || !empty($client->is_deleted)) {
            return false;
        }

        return $invitation;
    }

    /**
     * @param $account
     * @param $productKey
     * @return mixed
     */
    public function getProductDetail($account, $productKey = null)
    {
        if (empty($account) || empty($productKey)) {
            return null;
        }

        $product = DB::table('products')
            ->where('account_id', $account->id)
            ->where('product_key', trim($productKey))
            ->where('deleted_at', null)
            ->first();

        return !empty($product) ? $product : null;
    }

    /**
     * @param $account
     * @param null $product
     * @return mixed
     */
    public function getItemStore($account, $product = null)
    {
        if (empty($account) || empty($product)) {
            return;
        }

        $warehouseId = !empty(auth::user()->branch->warehouse_id) ?
            auth::user()->branch->warehouse_id : null;

        $itemStore = ItemStore::scope()
            ->where('account_id', $account->id)
            ->where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->where('deleted_at', null)
            ->first();

        return !empty($itemStore) ? $itemStore : null;
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function findOpenInvoices($clientId)
    {
        if (empty($clientId)) {
            return null;
        }
        $query = Invoice::scope()
            ->invoiceType(INVOICE_TYPE_STANDARD)
            ->where('client_id', $clientId)
            ->where('is_recurring', false)
            ->where('deleted_at', null)
            ->where('balance', '>', 0);

        return $query->where('invoice_status_id', '<', INVOICE_STATUS_PAID)
            ->select(['public_id', 'invoice_number'])
            ->get();
    }

    /**
     * @param Invoice $recurInvoice
     * @return mixed
     */
    public function createRecurringInvoice(Invoice $recurInvoice)
    {
        if (empty($recurInvoice)) {
            return null;
        }

        $recurInvoice->load('account.timezone', 'invoice_items', 'client', 'user');
        $client = $recurInvoice->client;

        if ($client->deleted_at) {
            return false;
        }

        if (!!empty($recurInvoice->user->confirmed)) {
            return false;
        }

        if (!$recurInvoice->shouldSendToday()) {
            return false;
        }

        $invoice = Invoice::createNew($recurInvoice);
        $invoice->is_public = true;
        $invoice->invoice_type_id = INVOICE_TYPE_STANDARD;
        $invoice->client_id = $recurInvoice->client_id;
        $invoice->recurring_invoice_id = $recurInvoice->id;
        $invoice->invoice_number = $recurInvoice->account->getClientNextNumber($invoice);
        $invoice->amount = $recurInvoice->amount;
        $invoice->balance = $recurInvoice->amount;
        $invoice->invoice_date = date_create()->format('Y-m-d');
        $invoice->discount = $recurInvoice->discount;
        $invoice->po_number = $recurInvoice->po_number;
        $invoice->public_notes = Utils::processVariables($recurInvoice->public_notes, $client);
        $invoice->terms = Utils::processVariables($recurInvoice->terms ?: $recurInvoice->account->invoice_terms, $client);
        $invoice->invoice_footer = Utils::processVariables($recurInvoice->invoice_footer ?: $recurInvoice->account->invoice_footer, $client);
        $invoice->tax_name1 = $recurInvoice->tax_name1;
        $invoice->tax_rate1 = $recurInvoice->tax_rate1;
        $invoice->tax_name2 = $recurInvoice->tax_name2;
        $invoice->tax_rate2 = $recurInvoice->tax_rate2;
        $invoice->invoice_design_id = $recurInvoice->invoice_design_id;
        $invoice->custom_value1 = $recurInvoice->custom_value1 ?: 0;
        $invoice->custom_value2 = $recurInvoice->custom_value2 ?: 0;
        $invoice->custom_taxes1 = $recurInvoice->custom_taxes1 ?: 0;
        $invoice->custom_taxes2 = $recurInvoice->custom_taxes2 ?: 0;
        $invoice->custom_text_value1 = Utils::processVariables($recurInvoice->custom_text_value1, $client);
        $invoice->custom_text_value2 = Utils::processVariables($recurInvoice->custom_text_value2, $client);
        $invoice->is_amount_discount = $recurInvoice->is_amount_discount;
        $invoice->due_date = $recurInvoice->getDueDate();
        $invoice->save();

        foreach ($recurInvoice->invoice_items as $recurItem) {
            $item = InvoiceItem::createNew($recurItem);
            $item->product_id = $recurItem->product_id;
            $item->qty = $recurItem->qty;
            $item->cost = $recurItem->cost;
            $item->notes = Utils::processVariables($recurItem->notes, $client);
            $item->product_key = Utils::processVariables($recurItem->product_key, $client);
            $item->tax_name1 = $recurItem->tax_name1;
            $item->tax_rate1 = $recurItem->tax_rate1;
            $item->tax_name2 = $recurItem->tax_name2;
            $item->tax_rate2 = $recurItem->tax_rate2;
            $item->custom_value1 = Utils::processVariables($recurItem->custom_value1, $client);
            $item->custom_value2 = Utils::processVariables($recurItem->custom_value2, $client);
            $item->discount = $recurItem->discount;

            $invoice->invoice_items()->save($item);
        }

        foreach ($recurInvoice->documents as $recurDocument) {
            $document = $recurDocument->cloneDocument();
            $invoice->documents()->save($document);
        }

        foreach ($recurInvoice->invitations as $recurInvitation) {
            $invitation = Invitation::createNew($recurInvitation);
            $invitation->contact_id = $recurInvitation->contact_id;
            $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $invoice->invitations()->save($invitation);
        }

        $recurInvoice->last_sent_date = date('Y-m-d');
        $recurInvoice->save();

        if ($recurInvoice->getAutoBillEnabled() && !$recurInvoice->account->auto_bill_on_due_date) {
// autoBillInvoice will check for ACH, so we're not checking here
            if ($this->paymentService->autoBillInvoice($invoice)) {
// update the invoice reference to match its actual state
// this is to ensure a 'payment received' email is sent
                $invoice->invoice_status_id = INVOICE_STATUS_PAID;
            }
        }

        $this->dispatchEvents($invoice);

        return $invoice;
    }

    /**
     * @param Account $account
     * @param bool $filterEnabled
     * @return Collection
     */
    public function findNeedingReminding(Account $account, $filterEnabled = true)
    {
        if (empty($account)) {
            return null;
        }
        $dates = [];
        for ($i = 1; $i <= 3; $i++) {
            if ($date = $account->getReminderDate($i, $filterEnabled)) {
                if ($account->{"field_reminder{$i}"} == REMINDER_FIELD_DUE_DATE) {
                    $dates[] = "(due_date = '$date' OR partial_due_date = '$date')";
                } else {
                    $dates[] = "invoice_date = '$date'";
                }
            }
        }

        if (!count($dates)) {
            return collect();
        }

        $sql = implode(' OR ', $dates);
        $invoices = Invoice::invoiceType(INVOICE_TYPE_STANDARD)
            ->with('client', 'invoice_items')
            ->whereHas('client', function ($query) {
                $query->where('send_reminders', true);
            })
            ->where('account_id', $account->id)
            ->where('balance', '>', 0)
            ->where('is_recurring', false)
            ->where('is_public', true)
            ->whereRaw('(' . $sql . ')')
            ->get();

        return $invoices;
    }

    /**
     * @param Account $account
     * @return Collection
     */
    public function findNeedingEndlessReminding(Account $account)
    {
        if (empty($account)) {
            return false;
        }

        $settings = $account->account_email_settings;
        $frequencyId = $settings->frequency_id_reminder4;

        if (!empty($frequencyId) || !!empty($account->enable_reminder4)) {
            return collect();
        }

        $frequency = Utils::getFromCache($frequencyId, 'frequencies');
        $lastSentDate = date_create();
        $lastSentDate->sub(date_interval_create_from_date_string($frequency->date_interval));

        $invoices = Invoice::invoiceType(INVOICE_TYPE_STANDARD)
            ->with('client', 'invoice_items')
            ->whereHas('client', function ($query) {
                $query->where('send_reminders', true);
            })
            ->where('account_id', $account->id)
            ->where('balance', '>', 0)
            ->where('is_recurring', false)
            ->where('is_public', true)
            ->where('last_sent_date', '<', $lastSentDate);

        for ($i = 1; $i <= 3; $i++) {
            if (!$account->{"enable_reminder{$i}"}) {
                continue;
            }
            $field = $account->{"field_reminder{$i}"} == REMINDER_FIELD_DUE_DATE ? 'due_date' : 'invoice_date';
            $date = date_create();
            if ($account->{"direction_reminder{$i}"} == REMINDER_DIRECTION_AFTER) {
                $date->sub(date_interval_create_from_date_string($account->{"num_days_reminder{$i}"} . ' days'));
            }
            $invoices->where($field, '<', $date);
        }

        return $invoices->get();
    }

    /**
     * @param $invoice
     * @return mixed|null
     */
    public function clearGatewayFee($invoice)
    {
        if (empty($invoice)) {
            return false;
        }

        $account = $invoice->account;

        if (!$invoice->relationLoaded('invoice_items')) {
            $invoice->load('invoice_items');
        }

        $data = $invoice->toArray();
        foreach ($data['invoice_items'] as $key => $item) {
            if ($item['invoice_item_type_id'] == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
                unset($data['invoice_items'][$key]);
                $this->save($data, $invoice);
                break;
            }
        }

        return true;
    }

    /**
     * @param $invoice
     * @param $amount
     * @param $percent
     * @return mixed|null
     */
    public function setLateFee($invoice, $amount, $percent)
    {
        if (empty($invoice)) {
            return false;
        }

        if ($amount <= 0 && $percent <= 0) {
            return false;
        }

        $account = $invoice->account;

        $data = $invoice->toArray();
        $fee = $amount;

        if ($invoice->getRequestedAmount() > 0) {
            $fee += round($invoice->getRequestedAmount() * $percent / 100, 2);
        }

        $item = [];
        $item['product_key'] = trans('texts.fee');
        $item['notes'] = trans('texts.late_fee_added', ['date' => $account->formatDate('now')]);
        $item['qty'] = 1;
        $item['cost'] = $fee;
        $item['invoice_item_type_id'] = INVOICE_ITEM_TYPE_LATE_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $invoice);

        return true;
    }

    /**
     * @param $invoice
     * @param $gatewayTypeId
     * @return mixed|null
     */
    public function setGatewayFee($invoice, $gatewayTypeId)
    {
        if (empty($invoice)) {
            return false;
        }

        $account = $invoice->account;

        if (!!empty($account->gateway_fee_enabled)) {
            return false;
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);
        $this->clearGatewayFee($invoice);

        if (empty($settings)) {
            return false;
        }

        $data = $invoice->toArray();
        $fee = $invoice->calcGatewayFee($gatewayTypeId);
        $date = $account->getDateTime()->format($account->getCustomDateFormat());
        $feeItemLabel = $account->getLabel('gateway_fee_item') ?: ($fee >= 0 ? trans('texts.surcharge') : trans('texts.discount'));

        if ($feeDescriptionLabel = $account->getLabel('gateway_fee_description')) {
            if (strpos($feeDescriptionLabel, '$date') !== false) {
                $feeDescriptionLabel = str_replace('$date', $date, $feeDescriptionLabel);
            } else {
                $feeDescriptionLabel .= ' • ' . $date;
            }
        } else {
            $feeDescriptionLabel = $fee >= 0 ? trans('texts.online_payment_surcharge') : trans('texts.online_payment_discount');
            $feeDescriptionLabel .= ' • ' . $date;
        }

        $item = [];
        $item['product_key'] = $feeItemLabel;
        $item['notes'] = $feeDescriptionLabel;
        $item['qty'] = 1;
        $item['cost'] = $fee;
        $item['tax_rate1'] = $settings->fee_tax_rate1;
        $item['tax_name1'] = $settings->fee_tax_name1;
        $item['tax_rate2'] = $settings->fee_tax_rate2;
        $item['tax_name2'] = $settings->fee_tax_name2;
        $item['invoice_item_type_id'] = INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $invoice);

        return true;
    }

    /**
     * @param $invoiceNumber
     * @return mixed|null
     */
    public function findPhonetically($invoiceNumber)
    {
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $invoiceId = 0;

        $invoices = Invoice::scope()->get(['id', 'invoice_number', 'public_id']);

        foreach ($invoices as $invoice) {
            $map[$invoice->id] = $invoice;
            $similar = similar_text($invoiceNumber, $invoice->invoice_number, $percent);
            if ($percent > $max) {
                $invoiceId = $invoice->id;
                $max = $percent;
            }
        }

        return ($invoiceId && !empty($map[$invoiceId])) ? $map[$invoiceId] : null;
    }

    /**
     * @param Invoice $invoice
     * @param array $item
     * @return mixed|null
     */
    private function getExpense(Invoice $invoice, array $item)
    {
        if (empty($item['expense_public_id'])) {
            return false;
        }

        $expense = Expense::scope($item['expense_public_id'])
            ->where('invoice_id', null)->firstOrFail();
        if (Auth::user()->can('edit', $expense)) {
            $expense->invoice_id = $invoice->id;
            $expense->client_id = $invoice->client_id;
            if ($expense->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Invoice $invoice
     * @param array $item
     * @return mixed|null
     */
    private function getTask(Invoice $invoice, array $item)
    {
        if (empty($item['task_public_id'])) {
            return false;
        }

        $task = Task::scope(trim($item['task_public_id']))
            ->where('invoice_id', null)->firstOrFail();
        if (Auth::user()->can('edit', $task)) {
            $task->invoice_id = $invoice->id;
            $task->client_id = $invoice->client_id;
            if ($task->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Invoice $invoice
     * @param array $document_ids
     * @return mixed|null
     */
    private function saveInvoiceDocuments(Invoice $invoice, array $document_ids)
    {
        // invoice docs
        if (empty($invoice) || empty($document_ids)) {
            return false;
        }

        foreach ($document_ids as $document_id) {
            $document = Document::scope($document_id)->first();
            if ($document && Auth::user()->can('edit', $document)) {
                if ($document->invoice_id && $document->invoice_id != $invoice->id) {
// From a clone
                    $document = $document->cloneDocument();
                    $document_ids[] = $document->public_id; // Don't remove this document
                }
                $document->invoice_id = $invoice->id;
                $document->expense_id = null;
                $document->save();
            }
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @param array $document_ids
     * @return mixed|null
     */
    private function updateInvoiceDocuments(Invoice $invoice, array $document_ids)
    {
        if (empty($invoice) || empty($document_ids)) {
            return false;
        }
        if (!$invoice->wasRecentlyCreated) {
            foreach ($invoice->documents as $document) {
                if (!in_array($document->public_id, $document_ids)) {
                    if (Auth::user()->can('delete', $document)) {
// Not checking permissions; deleting a document is just editing the invoice
                        if ($document->invoice_id === $invoice->id) {
// Make sure the document isn't on a clone
                            $document->delete();
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param $itemStore
     * @param Invoice $invoice
     * @param array $origLineItems
     * @param array $newLineItem
     * @param bool $isNew
     * @return mixed|null
     */
    private function stockAdjustment($itemStore, Invoice $invoice, $origLineItems, array $newLineItem, $isNew)
    {
        $qoh = !empty($itemStore) ? Utils::parseFloat($itemStore->qty) : 0;
        $demandQty = Utils::parseFloat(trim($newLineItem['qty']));

//        $Bill = Bill::where('product_key',$productKey);
//        $orderQty = Utils::parseFloat(0);
        if ($isNew) {
            $this->updateItemStore($qoh, $demandQty, $itemStore);
        } else {
            $found = 0;
            $origLineItems = (array)$origLineItems;
            if (count($origLineItems)) {
                foreach ($origLineItems as $origLineItem) {
                    if ($newLineItem['product_key'] === $origLineItem['product_key']) {
                        if (($newLineItem['qty'] != $origLineItem['qty'])) {
                            $qoh += $origLineItem['qty'];
                            $this->updateItemStore($qoh, $demandQty, $itemStore);
                            $found += 1;
                            break;
                        } else {
                            $found += 1;
                        }
                        break;
                    }
                }
                if ($found === 0) {
                    $this->updateItemStore($qoh, $demandQty, $itemStore);
                }
            } else {
                $this->updateItemStore($qoh, $demandQty, $itemStore);
            }
        }

        return true;
    }

    /**
     * @param $product
     * @param $itemStore
     * @param Invoice $invoice
     * @param array $item
     * @return mixed|null
     */
    private function saveInvoiceLineItemAdjustment($product, $itemStore, Invoice $invoice, array $item)
    {
        $invoicedQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $demandQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $itemCost = !empty($item['cost']) ? Utils::parseFloat(trim($item['cost'])) : (!empty($product->cost) ? $product->cost : 0);
        $invoiceItem = InvoiceItem::createNew($invoice);
        $invoiceItem->fill($item);
        $invoiceItem->product_id = !empty($product) ? $product->id : null;
        $invoiceItem->product_key = !empty($item['product_key']) ? trim($item['product_key']) : null;
        $invoiceItem->notes = !empty($item['notes']) ? trim($item['notes']) : null;
        $invoiceItem->cost = $itemCost;
        $invoiceItem->qty = $invoicedQty;
        $invoiceItem->demand_qty = $demandQty;
        $invoiceItem->discount = $invoice->discount;
        $invoiceItem->warehouse_id = $itemStore->warehouse_id;
        $invoiceItem->created_by = auth::user()->username;
        $qoh = !empty($itemStore->qty) ? Utils::parseFloat($itemStore->qty) : 0;
        if (!empty($itemStore) && $qoh < 1) {
            return false;
        }
        if ($invoicedQty > $qoh) {
            $invoiceItem->qty = $qoh;
        }

        if (!empty($item['custom_value1'])) {
            $invoiceItem->custom_value1 = $item['custom_value1'];
        }
        if (!empty($item['custom_value2'])) {
            $invoiceItem->custom_value2 = $item['custom_value2'];
        }
// provide backwards compatibility
        if (!empty($item['tax_name']) && !empty($item['tax_rate'])) {
            $item['tax_name1'] = $item['tax_name'];
            $item['tax_rate1'] = $item['tax_rate'];
        }

// provide backwards compatibility
        if (!empty($item['invoice_item_type_id']) && in_array($invoiceItem->notes, [trans('texts.online_payment_surcharge'), trans('texts.online_payment_discount')])) {
            $invoiceItem->invoice_item_type_id = $invoice->balance > 0 ? INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE : INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
        }

        $invoiceItem->fill($item);

        $invoice->invoice_items()->save($invoiceItem);

        return true;

    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @param $account
     * @return mixed|null
     */
    private function saveAccountDefault($account, Invoice $invoice, array $data)
    {
        if (empty($invoice)) {
            return false;
        }

        if ((!empty($data['set_default_terms']) && $data['set_default_terms'])
            || (!empty($data['set_default_footer']) && $data['set_default_footer'])) {
            if (!empty($data['set_default_terms']) && $data['set_default_terms']) {
                $account->{"{$invoice->getEntityType()}_terms"} = trim($data['terms']);
            }
            if (!empty($data['set_default_footer']) && $data['set_default_footer']) {
                $account->invoice_footer = trim($data['invoice_footer']);
            }

            $account->save();
        }

        return true;
    }

    /**
     * @param $account
     * @param array $data
     * @param Invoice $invoice
     * @return mixed|null
     */
    private function getLineItemNetTotal($account, Invoice $invoice, array $data)
    {
        $total = 0;
        $data = (array)$data;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                if (empty($item['cost']) && empty($item['product_key'])) {
                    continue;
                }
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $itemStore = $this->getItemStore($account, $product);
                    if (!empty($itemStore)) {
                        $invoiceItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                        $invoiceItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                        $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
//                 if quantity on hand greater than quantity demand
                        $qoh = Utils::roundSignificant(Utils::parseFloat($itemStore->qty));
                        if ($invoiceItemQty > $qoh) {
                            $invoiceItemQty = $qoh;
                        }
                        $total = $this->getLineItemTotal($invoice, $invoiceItemCost, $invoiceItemQty, $discount, $total);
                    }
                } else {
                    $total = $this->getLineItemTotal($invoice, trim($item['cost']), trim($item['qty']), trim($item['discount']), $total);
                }
            }
        }

        return $total;
    }

    /**
     * @param $account
     * @param array $data
     * @param Invoice $invoice
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemNetTax($account, Invoice $invoice, array $data, $total)
    {
        $itemTax = 0;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $itemStore = $this->getItemStore($account, $product);
                    if (!empty($itemStore)) {
                        $invoiceItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                        $invoiceItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                        $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                        $qoh = Utils::roundSignificant(Utils::parseFloat($itemStore->qty));
                        if ($invoiceItemQty > $qoh) {
                            $invoiceItemQty = $qoh;
                        }

                        $itemTax = $this->getLineItemTaxTotal($invoice, $total, $invoiceItemCost, $invoiceItemQty, $item, $itemTax);
                    }
                } else {
                    $itemTax = $this->getLineItemTaxTotal($invoice, $total, trim($item['cost']), trim($item['qty']), $item, $itemTax);
                }
            }
        }

        return $itemTax;
    }

    /**
     * @param float $qoh
     * @param float $demandQty
     * @param $itemStore
     * @return mixed|null
     */
    private function updateItemStore($qoh, $demandQty, $itemStore)
    {
        $qoh = Utils::parseFloat($qoh);
        $demandQty = Utils::parseFloat($demandQty);
        if ($qoh >= $demandQty) {
            $itemStore->qty = ($qoh - $demandQty);
            $itemStore->save();
        } else {
            if ($qoh < $demandQty) {
                $itemStore->qty = 0;
                $itemStore->save();
            }
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @param float $invoiceItemCost
     * @param float $invoiceItemQty
     * @param $discount
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemTotal(Invoice $invoice, $invoiceItemCost, $invoiceItemQty, $discount, $total)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $discount = !empty($discount) ? Utils::parseFloat($discount) : 0;
        $lineTotal = floatval($invoiceItemCost) * floatval($invoiceItemQty);
        if ($discount) {
            if (!empty($invoice->is_amount_discount)) {
                $lineTotal -= Utils::parseFloat($discount);
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }

        $total += round($lineTotal, 2);

        return $total;
    }

    /**
     * @param Invoice $invoice
     * @param float $total
     * @param float $invoiceItemCost
     * @param float $invoiceItemQty
     * @param array $item
     * @param float $itemTax
     * @return mixed|null
     */
    private function getLineItemTaxTotal(Invoice $invoice, $total, $invoiceItemCost, $invoiceItemQty, array $item, $itemTax)
    {
        $total = Utils::parseFloat($total);
        $itemTax = Utils::parseFloat($itemTax);
        $discount = !empty($item['discount']) ? round(Utils::parseFloat($item['discount']), 2) : 0;
        $lineTotal = floatval($invoiceItemCost) * floatval($invoiceItemQty);
        if ($discount) {
            if (!empty($invoice->is_amount_discount)) {
                $lineTotal -= $discount;
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }
//          if any invoice discount
        $invoiceDiscount = !empty($invoice->discount) ? Utils::parseFloat($invoice->discount) : 0;

        if ($invoiceDiscount) {
            if (!empty($invoice->is_amount_discount)) {
                if (!empty($total) && $total > 0) {
                    $lineTotal -= round($lineTotal / $total * $invoiceDiscount, 4);
                }
            } else {
                $lineTotal -= round(($lineTotal * $invoiceDiscount / 100), 4);
            }
        }
        if (!empty($item['tax_rate1'])) {
            $taxRate1 = Utils::parseFloat($item['tax_rate1']);
            if ($taxRate1 != 0) {
                $itemTax += round($lineTotal * $taxRate1 / 100, 2);
            }
        }
        if (!empty($item['tax_rate2'])) {
            $taxRate2 = Utils::parseFloat($item['tax_rate2']);
            if ($taxRate2 != 0) {
                $itemTax += round($lineTotal * $taxRate2 / 100, 2);
            }
        }

        return $itemTax;
    }

    /**
     * update invoice line item
     * @param $account
     * @param array $data
     * @param Invoice $invoice
     * @param $origLineItems
     * @param bool $isNew
     * @return mixed|null
     */
    private function saveLineItemDetail($account, Invoice $invoice, array $data, $origLineItems, $isNew)
    {
        if (empty($invoice)) {
            return false;
        }
        $product = null;
        $itemStore = null;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                if (empty($item['product_key']) && empty($item['cost'])) {
                    continue;
                }
                if (!empty($data['has_tasks'])) {
                    $this->getTask($invoice, $item);
                }
                if (!empty($data['has_expenses'])) {
                    $this->getExpense($invoice, $item);
                }
                $product = $this->getProductDetail($account, $item['product_key']);
//              item if not service and labor
                if (!empty($product) && $product->item_type_id !== SERVICE_OR_LABOUR) {
                    $itemStore = $this->getItemStore($account, $product);
                    if (!empty($itemStore)) {
                        // i couldn't find efficient evaluation for false expression, $data['has_tasks']== false and empty value
                        $is_quote = empty($data['is_quote']) ? $data['is_quote'] : null;
                        //  has taks empty value cannot be evaluated
                        $has_tasks = $data['has_tasks'] ? $data['has_tasks'] : null;
//                  what if invoices, quotes, expenses and tasks
                        if (empty($data['is_quote'])) {
                            $this->stockAdjustment($itemStore, $invoice, $origLineItems, $item, $isNew);
                        }
                        $this->saveInvoiceLineItemAdjustment($product, $itemStore, $invoice, $item);
                    }
                } else {
                    $this->saveInvoiceLineItemAdjustment($product, $itemStore, $invoice, $item);
                }
            }
        }

        return true;
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @param float $total
     * @param $account
     * @param $itemTax
     * @param bool $publicId
     * @return mixed|null
     */
    private function saveSaleInvoiceDetail($account, Invoice $invoice, array $data, $total, $itemTax, $publicId)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $invoiceDiscount = !empty($invoice->discount) ? Utils::parseFloat($invoice->discount) : 0;
//      if any invoice discount
        if ($invoiceDiscount) {
            if (!empty($invoice->is_amount_discount)) {
                $total -= $invoiceDiscount;
            } else {
                $discount = round($total * ($invoiceDiscount / 100), 2);
                $total -= $discount;
            }
        }

        if (!empty($data['custom_value1'])) {
            $invoice->custom_value1 = round($data['custom_value1'], 2);
        }
        if (!empty($data['custom_value2'])) {
            $invoice->custom_value2 = round($data['custom_value2'], 2);
        }

        if (!empty($data['custom_text_value1'])) {
            $invoice->custom_text_value1 = trim($data['custom_text_value1']);
        }
        if (!empty($data['custom_text_value2'])) {
            $invoice->custom_text_value2 = trim($data['custom_text_value2']);
        }

// custom fields charged taxes
        if ($invoice->custom_value1 && $invoice->custom_taxes1) {
            $total += $invoice->custom_value1;
        }
        if ($invoice->custom_value2 && $invoice->custom_taxes2) {
            $total += $invoice->custom_value2;
        }

        if (!empty($account->inclusive_taxes)) {
            $taxAmount1 = round($total * ($invoice->tax_rate1 ? $invoice->tax_rate1 : 0) / 100, 2);
            $taxAmount2 = round($total * ($invoice->tax_rate2 ? $invoice->tax_rate2 : 0) / 100, 2);

            $total = round($total + $taxAmount1 + $taxAmount2, 2);
            $total += $itemTax;
        }

// custom fields not charged taxes
        if ($invoice->custom_value1 && !$invoice->custom_taxes1) {
            $total += $invoice->custom_value1;
        }
        if ($invoice->custom_value2 && !$invoice->custom_taxes2) {
            $total += $invoice->custom_value2;
        }

        if (!empty($publicId)) {
            $invoice->balance = round($total - ($invoice->amount - $invoice->balance), 2);
        } else {
            $invoice->balance = $total;
        }

        if (!empty($data['partial'])) {
            $invoice->partial = max(0, min(round(Utils::parseFloat($data['partial']), 2), $invoice->balance));
        }

        if (!empty($invoice->partial)) {
            if (!empty($data['partial_due_date'])) {
                $invoice->partial_due_date = Utils::toSqlDate($data['partial_due_date']);
            }
        } else {
            $invoice->partial_due_date = null;
        }

        $invoice->amount = $total;

        $invoice = $invoice->save();

        return $invoice;

    }


}
