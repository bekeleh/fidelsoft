<?php

namespace App\Ninja\Repositories;

use App\Events\PurchaseInvoiceItemsWereCreated;
use App\Events\PurchaseInvoiceItemsWereUpdated;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Jobs\SendPurchaseInvoiceEmail;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Vendor;
use App\Models\Document;
use App\Models\EntityModel;
use App\Models\Expense;
use App\Models\PurchaseInvitation;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\ItemStore;
use App\Models\Task;
use Datatable;
use App\Services\PaymentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceRepository extends BaseRepository
{
    protected $documentRepo;
    protected $model;
    protected $paymentService;
    protected $paymentRepo;

    public function __construct(
        PurchaseInvoice $model,
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
        return 'App\Models\PurchaseInvoice';
    }

    public function all()
    {
        return PurchaseInvoice::scope()
            ->invoiceType(INVOICE_TYPE_STANDARD)
            ->with('user', 'vendor.contacts', 'invoice_status')
            ->withTrashed()->where('is_recurring', false)->get();
    }

    /**
     * @param bool $accountId
     * @param bool $vendorPublicId
     * @param string $entityType
     * @param bool $filter
     * @return mixed|null
     */
    public function getPurchaseInvoices($accountId = false, $vendorPublicId = false, $entityType = ENTITY_PURCHASE_INVOICE, $filter = false)
    {
        $query = DB::table('purchase_invoices')
            ->LeftJoin('accounts', 'accounts.id', '=', 'purchase_invoices.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'purchase_invoices.vendor_id')
            ->leftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'purchase_invoices.invoice_status_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('purchase_invoices.account_id', $accountId)
            ->where('vendor_contacts.deleted_at', null)
            ->where('purchase_invoices.is_recurring', false)
            ->where('vendor_contacts.is_primary', true)
//->whereRaw('(vendors.name != "" or vendor_contacts.first_name != "" or vendor_contacts.last_name != "" or vendor_contacts.email != "")') // filter out buy now purchase_invoices
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                'vendors.user_id as vendor_user_id',
                'invoice_number',
                'invoice_number as quote_number',
                'invoice_status_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'purchase_invoices.public_id',
                'purchase_invoices.amount',
                'purchase_invoices.balance',
                'purchase_invoices.discount',
                'purchase_invoices.invoice_date',
                'purchase_invoices.due_date as due_date_sql',
                'purchase_invoices.partial_due_date',
                DB::raw("CONCAT(purchase_invoices.invoice_date, purchase_invoices.created_at) as date"),
                DB::raw("CONCAT(COALESCE(purchase_invoices.partial_due_date, purchase_invoices.due_date), purchase_invoices.created_at) as due_date"),
                DB::raw("CONCAT(COALESCE(purchase_invoices.partial_due_date, purchase_invoices.due_date), purchase_invoices.created_at) as valid_until"),
                'invoice_statuses.name as status',
                'invoice_statuses.name as invoice_status_name',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'purchase_invoices.quote_id',
                'purchase_invoices.quote_invoice_id',
                'purchase_invoices.deleted_at',
                'purchase_invoices.is_deleted',
                'purchase_invoices.partial',
                'purchase_invoices.user_id',
                'purchase_invoices.is_public',
                'purchase_invoices.is_recurring',
                'purchase_invoices.private_notes',
                'purchase_invoices.public_notes',
                'purchase_invoices.created_at',
                'purchase_invoices.updated_at',
                'purchase_invoices.deleted_at',
                'purchase_invoices.created_by',
                'purchase_invoices.updated_by',
                'purchase_invoices.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('purchase_invoices.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('invoice_statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, $entityType);

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
                        $query->where('purchase_invoices.balance', '>', 0)
                            ->where('purchase_invoices.is_public', true);
                    });
                }
                if (in_array(INVOICE_STATUS_OVERDUE, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('purchase_invoices.balance', '>', 0)
                            ->where('purchase_invoices.due_date', '<', date('Y-m-d'))
                            ->where('purchase_invoices.is_public', true);
                    });
                }
            });
        }

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        } else {
            $query->where('vendors.deleted_at', null);
        }

        return $query;
    }

    /**
     * @param bool $accountId
     * @param bool $vendorPublicId
     * @param bool $filter
     * @return mixed
     */
    public function getRecurringPurchaseInvoices($accountId = false, $vendorPublicId = false, $filter = false)
    {
        $query = DB::table('purchase_invoices')
            ->LeftJoin('accounts', 'accounts.id', '=', 'purchase_invoices.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'purchase_invoices.vendor_id')
            ->LeftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'purchase_invoices.invoice_status_id')
            ->leftJoin('frequencies', 'frequencies.id', '=', 'purchase_invoices.frequency_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('purchase_invoices.account_id', $accountId)
            ->where('purchase_invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('vendor_contacts.deleted_at', null)
            ->where('purchase_invoices.is_recurring', true)
            ->where('vendor_contacts.is_primary', true)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'purchase_invoices.public_id',
                'purchase_invoices.amount',
                'frequencies.name as frequency',
                'purchase_invoices.start_date as start_date_sql',
                'purchase_invoices.end_date as end_date_sql',
                'purchase_invoices.last_sent_date as last_sent_date_sql',
                DB::raw("CONCAT(purchase_invoices.start_date, purchase_invoices.created_at) as start_date"),
                DB::raw("CONCAT(purchase_invoices.end_date, purchase_invoices.created_at) as end_date"),
                DB::raw("CONCAT(purchase_invoices.last_sent_date, purchase_invoices.created_at) as last_sent"),
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'purchase_invoices.deleted_at',
                'purchase_invoices.is_deleted',
                'purchase_invoices.user_id',
                'invoice_statuses.name as invoice_status_name',
                'invoice_statuses.name as status',
                'purchase_invoices.invoice_status_id',
                'purchase_invoices.balance',
                'purchase_invoices.due_date',
                'purchase_invoices.due_date as due_date_sql',
                'purchase_invoices.is_recurring',
                'purchase_invoices.quote_invoice_id',
                'purchase_invoices.public_notes',
                'purchase_invoices.private_notes',
                'purchase_invoices.created_at',
                'purchase_invoices.updated_at',
                'purchase_invoices.deleted_at',
                'purchase_invoices.created_by',
                'purchase_invoices.updated_by',
                'purchase_invoices.deleted_by'
            );

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        } else {
            $query->where('vendors.deleted_at', null);
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('purchase_invoices.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%');
            });
        }

//       don't remove the third parameter unless invoice and recurring invoice are separated
        $this->applyFilters($query, ENTITY_RECURRING_PURCHASE_INVOICE, 'purchase_invoices');

        return $query;
    }

    /**
     * @param $contactId
     * @param null $filter
     * @return mixed
     */
    public function getVendorRecurringDatatable($contactId, $filter = null)
    {
        $query = DB::table('purchase_invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'purchase_invitations.account_id')
            ->LeftJoin('purchase_invoices', 'purchase_invoices.id', '=', 'purchase_invitations.invoice_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'purchase_invoices.vendor_id')
            ->LeftJoin('frequencies', 'frequencies.id', '=', 'purchase_invoices.frequency_id')
            ->where('purchase_invitations.contact_id', $contactId)
            ->where('purchase_invitations.deleted_at', null)
            ->where('purchase_invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('purchase_invoices.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('purchase_invoices.is_recurring', true)
            ->where('purchase_invoices.is_public', true)
            ->where('purchase_invoices.deleted_at', null)
//->where('purchase_invoices.start_date', '>=', date('Y-m-d H:i:s'))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'purchase_invitations.invitation_key',
                'purchase_invoices.invoice_number',
                'purchase_invoices.due_date',
                'vendors.public_id as vendor_public_id',
                'vendors.name as vendor_name',
                'purchase_invoices.public_id',
                'purchase_invoices.amount',
                'purchase_invoices.start_date',
                'purchase_invoices.end_date',
                'purchase_invoices.auto_bill',
                'purchase_invoices.vendor_enable_auto_bill',
                'frequencies.name as frequency',
                'purchase_invoices.created_at',
                'purchase_invoices.updated_at',
                'purchase_invoices.deleted_at',
                'purchase_invoices.created_by',
                'purchase_invoices.updated_by',
                'purchase_invoices.deleted_by'
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
            ->addColumn('vendor_enable_auto_bill', function ($model) {
                if ($model->auto_bill == AUTO_BILL_OFF) {
                    return trans('texts.disabled');
                } elseif ($model->auto_bill == AUTO_BILL_ALWAYS) {
                    return trans('texts.enabled');
                } elseif ($model->vendor_enable_auto_bill) {
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
    public function getVendorDatatable($contactId, $entityType, $search)
    {
        $query = DB::table('purchase_invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'purchase_invitations.account_id')
            ->LeftJoin('purchase_invoices', 'purchase_invoices.id', '=', 'purchase_invitations.invoice_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'purchase_invoices.vendor_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('purchase_invitations.contact_id', $contactId)
            ->where('purchase_invitations.deleted_at', null)
            ->where('purchase_invoices.invoice_type_id', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD)
            ->where('purchase_invoices.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('vendor_contacts.deleted_at', null)
            ->where('vendor_contacts.is_primary', true)
            ->where('purchase_invoices.is_recurring', false)
            ->where('purchase_invoices.is_public', true)
// Only show paid purchase_invoices for ninja accounts
//            ->whereRaw(sprintf("((accounts.account_key != '%s' and accounts.account_key not like '%s%%') or purchase_invoices.invoice_status_id = %d)", env('NINJA_LICENSE_ACCOUNT_KEY'), substr(NINJA_ACCOUNT_KEY, 0, 30), INVOICE_STATUS_PAID))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'purchase_invitations.invitation_key',
                'purchase_invoices.invoice_number',
                'purchase_invoices.invoice_date',
                'purchase_invoices.balance as balance',
                'purchase_invoices.due_date',
                'purchase_invoices.invoice_status_id',
                'purchase_invoices.due_date',
                'purchase_invoices.quote_invoice_id',
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'purchase_invoices.public_id',
                'purchase_invoices.amount',
                'purchase_invoices.start_date',
                'purchase_invoices.end_date',
                'purchase_invoices.partial',
                'purchase_invoices.created_at',
                'purchase_invoices.updated_at',
                'purchase_invoices.deleted_at',
                'purchase_invoices.created_by',
                'purchase_invoices.updated_by',
                'purchase_invoices.deleted_by'
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

        if ($entityType == ENTITY_PURCHASE_INVOICE) {
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
                } elseif (PurchaseInvoice::calcIsOverdue($model->balance, $model->due_date)) {
                    $class = 'danger';
                    if ($entityType == ENTITY_PURCHASE_INVOICE) {
                        $label = trans('texts.past_due');
                    } else {
                        $label = trans('texts.expired');
                    }
                } else {
                    $class = 'default';
                    if ($entityType == ENTITY_PURCHASE_INVOICE) {
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
     * @param PurchaseInvoice|null $purchaseInvoice
     * @return PurchaseInvoice
     */
    public function save(array $data, PurchaseInvoice $purchaseInvoice = null)
    {
        /** @var Account $account */
        $account = $purchaseInvoice ? $purchaseInvoice->account : Auth::user()->account;
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $isNew = !$publicId || intval($publicId) < 0;

        if (!empty($purchaseInvoice)) {
            $entityType = $purchaseInvoice->getEntityType();
            $purchaseInvoice->updated_by = Auth::user()->username;

        } elseif (!empty($isNew)) {
            $entityType = ENTITY_PURCHASE_INVOICE;
            if (!empty($data['is_recurring']) && filter_var($data['is_recurring'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_RECURRING_PURCHASE_INVOICE;
            } elseif (!empty($data['is_quote']) && filter_var($data['is_quote'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_QUOTE;
            }

            $purchaseInvoice = $account->createPurchaseInvoice($entityType, $data['vendor_id']);
            $purchaseInvoice->invoice_date = date_create()->format('Y-m-d');
            $purchaseInvoice->custom_taxes1 = $account->custom_invoice_taxes1 ?: false;
            $purchaseInvoice->custom_taxes2 = $account->custom_invoice_taxes2 ?: false;
            $purchaseInvoice->created_by = Auth::user()->username;
//           set the default due date
            if ($entityType === ENTITY_PURCHASE_INVOICE && !empty($data['partial_due_date'])) {
                $vendor = Vendor::scope()->where('id', $data['vendor_id'])->first();
                $purchaseInvoice->due_date = $account->defaultDueDate($vendor);
            }
        } else {
            $purchaseInvoice = PurchaseInvoice::scope($publicId)->firstOrFail();
        }
        if (!empty($purchaseInvoice->is_deleted)) {
            return $purchaseInvoice;
        } elseif ($purchaseInvoice->isLocked()) {
            return $purchaseInvoice;
        }

        if (isset($data['has_tasks']) && filter_var($data['has_tasks'], FILTER_VALIDATE_BOOLEAN)) {
            $purchaseInvoice->has_tasks = true;
        }
        if (isset($data['has_expenses']) && filter_var($data['has_expenses'], FILTER_VALIDATE_BOOLEAN)) {
            $purchaseInvoice->has_expenses = true;
        }

        if (isset($data['is_public']) && filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN)) {
            $purchaseInvoice->is_public = true;
            if (!$purchaseInvoice->isSent()) {
                $purchaseInvoice->invoice_status_id = INVOICE_STATUS_SENT;
            }
        }

//     TODO: should be examine this expression
        if ($data['invoice_design_id'] && !$data['invoice_design_id']) {
            $data['invoice_design_id'] = 1;
        }

//      fill invoice
        $purchaseInvoice->fill($data);

//      update account default template
        $this->saveAccountDefault($account, $purchaseInvoice, $data);

        if (!empty($data['invoice_number']) && !empty($purchaseInvoice->is_recurring)) {
            $purchaseInvoice->invoice_number = trim($data['invoice_number']);
        }

        if (isset($data['discount'])) {
            $purchaseInvoice->discount = round(Utils::parseFloat($data['discount']), 2);
        }
        if (isset($data['is_amount_discount'])) {
            $purchaseInvoice->is_amount_discount = $data['is_amount_discount'] ? true : false;
        }

        if (!empty($data['invoice_date_sql'])) {
            $purchaseInvoice->invoice_date = $data['invoice_date_sql'];
        } elseif (!empty($data['invoice_date'])) {
            $purchaseInvoice->invoice_date = Utils::toSqlDate($data['invoice_date']);
        }

        if (!empty($data['invoice_status_id'])) {
            if ($data['invoice_status_id'] == 0) {
                $data['invoice_status_id'] = INVOICE_STATUS_DRAFT;
            }
            $purchaseInvoice->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        } else {
            $purchaseInvoice->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        }
        if (!empty($purchaseInvoice->is_recurring)) {
            if ($isNew && !empty($data['start_date']) && !empty($purchaseInvoice->start_date)
                && $purchaseInvoice->start_date != Utils::toSqlDate($data['start_date'])) {
                $purchaseInvoice->last_sent_date = null;
            }

            $purchaseInvoice->frequency_id = array_get($data, 'frequency_id', FREQUENCY_MONTHLY);
            $purchaseInvoice->start_date = Utils::toSqlDate(array_get($data, 'start_date'));
            $purchaseInvoice->end_date = Utils::toSqlDate(array_get($data, 'end_date'));
            $purchaseInvoice->vendor_enable_auto_bill = !empty($data['vendor_enable_auto_bill']) && $data['vendor_enable_auto_bill'] ? true : false;
            $purchaseInvoice->auto_bill = array_get($data, 'auto_bill_id') ?: array_get($data, 'auto_bill', AUTO_BILL_OFF);

            if ($purchaseInvoice->auto_bill < AUTO_BILL_OFF || $purchaseInvoice->auto_bill > AUTO_BILL_ALWAYS) {
                $purchaseInvoice->auto_bill = AUTO_BILL_OFF;
            }

            if (!empty($data['recurring_due_date'])) {
                $purchaseInvoice->due_date = $data['recurring_due_date'];
            } elseif (!empty($data['due_date'])) {
                $purchaseInvoice->due_date = $data['due_date'];
            }
        } else {
            if ($isNew && empty($data['due_date']) && empty($data['due_date_sql'])) {
// do nothing
            } elseif (!empty($data['due_date']) || !empty($data['due_date_sql'])) {
                $purchaseInvoice->due_date = !empty($data['due_date_sql']) ? $data['due_date_sql'] :
                    Utils::toSqlDate($data['due_date']);
            }
// invoice is not recurring
            $purchaseInvoice->frequency_id = 0;
            $purchaseInvoice->start_date = null;
            $purchaseInvoice->end_date = null;
        }

        if (!empty($data['terms'])) {
            $purchaseInvoice->terms = trim($data['terms']);
        } elseif ($isNew && !empty($purchaseInvoice->is_recurring) && $account->{"{$entityType}_terms"}) {
            $purchaseInvoice->terms = $account->{"{$entityType}_terms"};
        } else {
            $purchaseInvoice->terms = '';
        }

        if (!empty($data['invoice_footer'])) {
            $purchaseInvoice->invoice_footer = trim($data['invoice_footer']);
        } elseif ($isNew && !empty($purchaseInvoice->is_recurring) && !empty($account->invoice_footer)) {
            $purchaseInvoice->invoice_footer = $account->invoice_footer;
        } else {
            $purchaseInvoice->invoice_footer = '';
        }

        $purchaseInvoice->public_notes = !empty($data['public_notes']) ? trim($data['public_notes']) : '';

// process date variables if not recurring
        if (!empty($purchaseInvoice->is_recurring)) {
            $purchaseInvoice->terms = Utils::processVariables($purchaseInvoice->terms);
            $purchaseInvoice->invoice_footer = Utils::processVariables($purchaseInvoice->invoice_footer);
            $purchaseInvoice->public_notes = Utils::processVariables($purchaseInvoice->public_notes);
        }

        if (!empty($data['po_number'])) {
            $purchaseInvoice->po_number = trim($data['po_number']);
        }

//    provide backwards compatibility
        if (!empty($data['tax_name']) && !empty($data['tax_rate'])) {
            $data['tax_name1'] = $data['tax_name'];
            $data['tax_rate1'] = $data['tax_rate'];
        }

//       line item total
        $total = 0;
        $total = $this->getLineItemNetTotal($account, $purchaseInvoice, $data);

//      line item tax
        $itemTax = 0;
        $itemTax = $this->getLineItemNetTax($account, $purchaseInvoice, $data, $total);

//       save invoice
        $this->getCalculatePurchaseInvoice($account, $purchaseInvoice, $data, $total, $itemTax, $publicId);

        $origLineItems = [];
        if (!empty($publicId)) {
            $origLineItems = !empty($purchaseInvoice->invoice_items) ?
                $purchaseInvoice->invoice_items()->get()->toArray() : null;
//            remove old invoice line items
            $purchaseInvoice->invoice_items()->forceDelete();
        }
//      update if any invoice documents
        if (!empty($data['document_ids'])) {
            $document_ids = array_map('intval', $data['document_ids']);
            $this->uploadedPurchaseInvoiceDocuments($purchaseInvoice, $document_ids);
            $this->updatePurchaseInvoiceDocuments($purchaseInvoice, $document_ids);

        }

//      core invoice computation
        $this->getCalculatePurchaseInvoiceItem($account, $purchaseInvoice, $data, $origLineItems, $isNew);

        $this->saveInvitations($purchaseInvoice);

//      finally dispatch events
        $this->dispatchEvents($purchaseInvoice);

        return $purchaseInvoice->load('invoice_items');
    }

    /**
     * @param $purchaseInvoice
     * @return mixed
     */
    private function saveInvitations($purchaseInvoice)
    {
        if (empty($purchaseInvoice)) {
            return null;
        }

        $vendor = $purchaseInvoice->vendor;

        $vendor->load('contacts');
        $sendPurchaseInvoiceIds = [];

        if (!$vendor->contacts->count()) {
            return $purchaseInvoice;
        }

        foreach ($vendor->contacts as $contact) {
            if ($contact->send_invoice) {
                $sendPurchaseInvoiceIds[] = $contact->id;
            }
        }

        // if no contacts are selected auto-select the first to ensure there's an invitation
        if (!count($sendPurchaseInvoiceIds)) {
            $sendPurchaseInvoiceIds[] = $vendor->contacts[0]->id;
        }

        foreach ($vendor->contacts as $contact) {
            $invitation = PurchaseInvitation::scope()->where('contact_id', $contact->id)
                ->wherePurchaseInvoiceId($purchaseInvoice->id)->first();
            if (in_array($contact->id, $sendPurchaseInvoiceIds) && empty($invitation)) {
                $invitation = PurchaseInvitation::createNew($purchaseInvoice);
                $invitation->invoice_id = $purchaseInvoice->id;
                $invitation->contact_id = $contact->id;
                $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                $invitation->save();
            } elseif (!in_array($contact->id, $sendPurchaseInvoiceIds) && !empty($invitation)) {
                $invitation->delete();
            }
        }

        if ($purchaseInvoice->is_public && !$purchaseInvoice->areInvitationsSent()) {
            $purchaseInvoice->markInvitationsSent();
        }

        return $purchaseInvoice;
    }

    /**
     * @param $purchaseInvoice
     * @return null
     */
    private function dispatchEvents($purchaseInvoice)
    {
        if (empty($purchaseInvoice)) {
            return null;
        }
        if ($purchaseInvoice->isType(INVOICE_TYPE_QUOTE)) {
            if ($purchaseInvoice->wasRecentlyCreated) {
                event(new QuoteItemsWereCreated($purchaseInvoice));
            } else {
                event(new QuoteItemsWereUpdated($purchaseInvoice));
            }
        } else {
            if ($purchaseInvoice->wasRecentlyCreated) {
                event(new PurchaseInvoiceItemsWereCreated($purchaseInvoice));
            } else {
                event(new PurchaseInvoiceItemsWereUpdated($purchaseInvoice));
            }
        }
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param null $quoteId
     * @return mixed
     */
    public function clonePurchaseInvoice(PurchaseInvoice $purchaseInvoice, $quoteId = null)
    {
        if (empty($purchaseInvoice)) {
            return null;
        }

        $purchaseInvoice->load('purchase_invitations', 'invoice_items');
        $account = $purchaseInvoice->account;

        $clone = PurchaseInvoice::createNew($purchaseInvoice);
        $clone->balance = $purchaseInvoice->amount;

// if the invoice prefix is diff than quote prefix, use the same number for the invoice (if it's available)
        $purchaseInvoiceNumber = false;
        if ($account->hasPurchaseInvoicePrefix() && $account->share_counter) {
            $purchaseInvoiceNumber = $purchaseInvoice->invoice_number;
            if ($account->quote_number_prefix && strpos($purchaseInvoiceNumber, $account->quote_number_prefix) === 0) {
                $purchaseInvoiceNumber = substr($purchaseInvoiceNumber, strlen($account->quote_number_prefix));
            }
            $purchaseInvoiceNumber = $account->invoice_number_prefix . $purchaseInvoiceNumber;
            $purchaseInvoice = PurchaseInvoice::scope(false, $account->id)
                ->withTrashed()
                ->where('invoice_number', $purchaseInvoiceNumber)
                ->first();
            if ($purchaseInvoice) {
                $purchaseInvoiceNumber = false;
            } else {
// since we aren't using the counter we need to offset it by one
                $account->invoice_number_counter -= 1;
                $account->save();
            }
        }

        foreach ([
                     'vendor_id',
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
            $clone->$field = $purchaseInvoice->$field;
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

        $clone->invoice_number = $purchaseInvoiceNumber ?: $account->getVendorNextNumber($clone);
        $clone->invoice_date = date_create()->format('Y-m-d');
        $clone->due_date = $account->defaultDueDate($purchaseInvoice->vendor);
        $clone->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
        $clone->save();

        if ($quoteId) {
            $purchaseInvoice->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
            $purchaseInvoice->quote_invoice_id = $clone->public_id;
            $purchaseInvoice->save();
        }

        foreach ($purchaseInvoice->invoice_items as $item) {
//          invoice item instance
            $cloneItem = PurchaseInvoiceItem::createNew($purchaseInvoice);
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

        foreach ($purchaseInvoice->documents as $document) {
            $cloneDocument = $document->cloneDocument();
            $clone->documents()->save($cloneDocument);
        }

        foreach ($purchaseInvoice->purchase_invitations as $invitation) {
            $cloneInvitation = PurchaseInvitation::createNew($purchaseInvoice);
            $cloneInvitation->contact_id = $invitation->contact_id;
            $cloneInvitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $clone->purchase_invitations()->save($cloneInvitation);
        }

        $this->dispatchEvents($clone);

        return $clone;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @return mixed|null
     */
    public function emailPurchaseInvoice(PurchaseInvoice $purchaseInvoice)
    {
        if (empty($purchaseInvoice)) {
            return null;
        }

        if (config('queue.default') === 'sync') {
            app('App\Ninja\Mailers\ContactMailer')->sendPurchaseInvoice($purchaseInvoice);
        } else {
            dispatch(new SendPurchaseInvoiceEmail($purchaseInvoice));
        }
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     */
    public function markSent(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->markSent();
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @return mixed|void|null
     */
    public function markPaid(PurchaseInvoice $purchaseInvoice)
    {
        if (!$purchaseInvoice->canBePaid()) {
            return null;
        }

        $purchaseInvoice->markSentIfUnsent();

        $data = [
            'vendor_id' => $purchaseInvoice->vendor_id,
            'invoice_id' => $purchaseInvoice->id,
            'amount' => $purchaseInvoice->balance,
        ];

        return $this->paymentRepo->save($data);
    }

    /**
     * @param $invitationKey
     * @return Invitation|mixed
     */
    public function findPurchaseInvoiceByInvitation($invitationKey)
    {
        if (empty($invitationKey)) {
            return null;
        }
// check for extra params at end of value (from website feature)
        list($invitationKey) = explode('&', $invitationKey);
        $invitationKey = substr($invitationKey, 0, RANDOM_KEY_LENGTH);

        $invitation = PurchaseInvitation::where('invitation_key', $invitationKey)->first();

        if (empty($invitation)) {
            return false;
        }

        $purchaseInvoice = $invitation->invoice;
        if (empty($purchaseInvoice) || isset($purchaseInvoice->is_deleted)) {
            return false;
        }

        $purchaseInvoice->load('user', 'invoice_items', 'documents', 'invoice_design', 'account.country', 'vendor.contacts', 'vendor.country');
        $vendor = $purchaseInvoice->vendor;

        if (empty($vendor) || isset($vendor->is_deleted)) {
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
     * @param $vendorId
     * @return mixed
     */
    public function findOpenPurchaseInvoices($vendorId)
    {
        if (empty($vendorId)) {
            return null;
        }
        $query = PurchaseInvoice::scope()
            ->invoiceType(INVOICE_TYPE_STANDARD)
            ->where('vendor_id', $vendorId)
            ->where('is_recurring', false)
            ->where('deleted_at', null)
            ->where('balance', '>', 0);

        return $query->where('invoice_status_id', '<', INVOICE_STATUS_PAID)
            ->select(['public_id', 'invoice_number'])
            ->get();
    }

    /**
     * @param PurchaseInvoice $recurPurchaseInvoice
     * @return mixed
     */
    public function createRecurringPurchaseInvoice(PurchaseInvoice $recurPurchaseInvoice)
    {
        if (empty($recurPurchaseInvoice)) {
            return null;
        }

        $recurPurchaseInvoice->load('account.timezone', 'invoice_items', 'vendor', 'user');
        $vendor = $recurPurchaseInvoice->vendor;

        if ($vendor->deleted_at) {
            return false;
        }

        if (!isset($recurPurchaseInvoice->user->confirmed)) {
            return false;
        }

        if (!$recurPurchaseInvoice->shouldSendToday()) {
            return false;
        }

        $purchaseInvoice = PurchaseInvoice::createNew($recurPurchaseInvoice);
        $purchaseInvoice->is_public = true;
        $purchaseInvoice->invoice_type_id = INVOICE_TYPE_STANDARD;
        $purchaseInvoice->vendor_id = $recurPurchaseInvoice->vendor_id;
        $purchaseInvoice->recurring_invoice_id = $recurPurchaseInvoice->id;
        $purchaseInvoice->invoice_number = $recurPurchaseInvoice->account->getVendorNextNumber($purchaseInvoice);
        $purchaseInvoice->amount = $recurPurchaseInvoice->amount;
        $purchaseInvoice->balance = $recurPurchaseInvoice->amount;
        $purchaseInvoice->invoice_date = date_create()->format('Y-m-d');
        $purchaseInvoice->discount = $recurPurchaseInvoice->discount;
        $purchaseInvoice->po_number = $recurPurchaseInvoice->po_number;
        $purchaseInvoice->public_notes = Utils::processVariables($recurPurchaseInvoice->public_notes, $vendor);
        $purchaseInvoice->terms = Utils::processVariables($recurPurchaseInvoice->terms ?: $recurPurchaseInvoice->account->invoice_terms, $vendor);
        $purchaseInvoice->invoice_footer = Utils::processVariables($recurPurchaseInvoice->invoice_footer ?: $recurPurchaseInvoice->account->invoice_footer, $vendor);
        $purchaseInvoice->tax_name1 = $recurPurchaseInvoice->tax_name1;
        $purchaseInvoice->tax_rate1 = $recurPurchaseInvoice->tax_rate1;
        $purchaseInvoice->tax_name2 = $recurPurchaseInvoice->tax_name2;
        $purchaseInvoice->tax_rate2 = $recurPurchaseInvoice->tax_rate2;
        $purchaseInvoice->invoice_design_id = $recurPurchaseInvoice->invoice_design_id;
        $purchaseInvoice->custom_value1 = $recurPurchaseInvoice->custom_value1 ?: 0;
        $purchaseInvoice->custom_value2 = $recurPurchaseInvoice->custom_value2 ?: 0;
        $purchaseInvoice->custom_taxes1 = $recurPurchaseInvoice->custom_taxes1 ?: 0;
        $purchaseInvoice->custom_taxes2 = $recurPurchaseInvoice->custom_taxes2 ?: 0;
        $purchaseInvoice->custom_text_value1 = Utils::processVariables($recurPurchaseInvoice->custom_text_value1, $vendor);
        $purchaseInvoice->custom_text_value2 = Utils::processVariables($recurPurchaseInvoice->custom_text_value2, $vendor);
        $purchaseInvoice->is_amount_discount = $recurPurchaseInvoice->is_amount_discount;
        $purchaseInvoice->due_date = $recurPurchaseInvoice->getDueDate();
        $purchaseInvoice->save();

        foreach ($recurPurchaseInvoice->invoice_items as $recurItem) {
            $item = PurchaseInvoiceItem::createNew($recurItem);
            $item->product_id = $recurItem->product_id;
            $item->qty = $recurItem->qty;
            $item->cost = $recurItem->cost;
            $item->notes = Utils::processVariables($recurItem->notes, $vendor);
            $item->product_key = Utils::processVariables($recurItem->product_key, $vendor);
            $item->tax_name1 = $recurItem->tax_name1;
            $item->tax_rate1 = $recurItem->tax_rate1;
            $item->tax_name2 = $recurItem->tax_name2;
            $item->tax_rate2 = $recurItem->tax_rate2;
            $item->custom_value1 = Utils::processVariables($recurItem->custom_value1, $vendor);
            $item->custom_value2 = Utils::processVariables($recurItem->custom_value2, $vendor);
            $item->discount = $recurItem->discount;

            $purchaseInvoice->invoice_items()->save($item);
        }

        foreach ($recurPurchaseInvoice->documents as $recurDocument) {
            $document = $recurDocument->cloneDocument();
            $purchaseInvoice->documents()->save($document);
        }

        foreach ($recurPurchaseInvoice->purchase_invitations as $recurInvitation) {
            $invitation = PurchaseInvitation::createNew($recurInvitation);
            $invitation->contact_id = $recurInvitation->contact_id;
            $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $purchaseInvoice->purchase_invitations()->save($invitation);
        }

        $recurPurchaseInvoice->last_sent_date = date('Y-m-d');
        $recurPurchaseInvoice->save();

        if ($recurPurchaseInvoice->getAutoBillEnabled() && !$recurPurchaseInvoice->account->auto_bill_on_due_date) {
// autoBillPurchaseInvoice will check for ACH, so we're not checking here
            if ($this->paymentService->autoBillPurchaseInvoice($purchaseInvoice)) {
// update the invoice reference to match its actual state
// this is to ensure a 'payment received' email is sent
                $purchaseInvoice->invoice_status_id = INVOICE_STATUS_PAID;
            }
        }

        $this->dispatchEvents($purchaseInvoice);

        return $purchaseInvoice;
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
        $purchase_invoices = PurchaseInvoice::invoiceType(INVOICE_TYPE_STANDARD)
            ->with('vendor', 'invoice_items')
            ->whereHas('vendor', function ($query) {
                $query->where('send_reminders', true);
            })
            ->where('account_id', $account->id)
            ->where('balance', '>', 0)
            ->where('is_recurring', '=', false)
            ->where('is_public', true)
            ->whereRaw('(' . $sql . ')')
            ->get();

        return $purchase_invoices;
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

        if (!empty($frequencyId) || !isset($account->enable_reminder4)) {
            return collect();
        }

        $frequency = Utils::getFromCache($frequencyId, 'frequencies');
        $lastSentDate = date_create();
        $lastSentDate->sub(date_interval_create_from_date_string($frequency->date_interval));

        $purchase_invoices = PurchaseInvoice::invoiceType(INVOICE_TYPE_STANDARD)
            ->with('vendor', 'invoice_items')
            ->whereHas('vendor', function ($query) {
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
            $purchase_invoices->where($field, '<', $date);
        }

        return $purchase_invoices->get();
    }

    /**
     * @param $purchaseInvoice
     * @return mixed|null
     */
    public function clearGatewayFee($purchaseInvoice)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $account = $purchaseInvoice->account;

        if (!$purchaseInvoice->relationLoaded('invoice_items')) {
            $purchaseInvoice->load('invoice_items');
        }

        $data = $purchaseInvoice->toArray();
        foreach ($data['invoice_items'] as $key => $item) {
            if ($item['invoice_item_type_id'] == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
                unset($data['invoice_items'][$key]);
                $this->save($data, $purchaseInvoice);
                break;
            }
        }

        return true;
    }

    /**
     * @param $purchaseInvoice
     * @param $amount
     * @param $percent
     * @return mixed|null
     */
    public function setLateFee($purchaseInvoice, $amount, $percent)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        if ($amount <= 0 && $percent <= 0) {
            return false;
        }

        $account = $purchaseInvoice->account;

        $data = $purchaseInvoice->toArray();
        $fee = $amount;

        if ($purchaseInvoice->getRequestedAmount() > 0) {
            $fee += round($purchaseInvoice->getRequestedAmount() * $percent / 100, 2);
        }

        $item = [];
        $item['product_key'] = trans('texts.fee');
        $item['notes'] = trans('texts.late_fee_added', ['date' => $account->formatDate('now')]);
        $item['qty'] = 1;
        $item['cost'] = $fee;
        $item['invoice_item_type_id'] = INVOICE_ITEM_TYPE_LATE_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $purchaseInvoice);

        return true;
    }

    /**
     * @param $purchaseInvoice
     * @param $gatewayTypeId
     * @return mixed|null
     */
    public function setGatewayFee($purchaseInvoice, $gatewayTypeId)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $account = $purchaseInvoice->account;

        if (!isset($account->gateway_fee_enabled)) {
            return false;
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);
        $this->clearGatewayFee($purchaseInvoice);

        if (empty($settings)) {
            return false;
        }

        $data = $purchaseInvoice->toArray();
        $fee = $purchaseInvoice->calcGatewayFee($gatewayTypeId);
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

        $this->save($data, $purchaseInvoice);

        return true;
    }

    /**
     * @param $purchaseInvoiceNumber
     * @return mixed|null
     */
    public function findPhonetically($purchaseInvoiceNumber)
    {
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $purchaseInvoiceId = 0;

        $purchase_invoices = PurchaseInvoice::scope()->get(['id', 'invoice_number', 'public_id']);

        foreach ($purchase_invoices as $purchaseInvoice) {
            $map[$purchaseInvoice->id] = $purchaseInvoice;
            $similar = similar_text($purchaseInvoiceNumber, $purchaseInvoice->invoice_number, $percent);
            if ($percent > $max) {
                $purchaseInvoiceId = $purchaseInvoice->id;
                $max = $percent;
            }
        }

        return ($purchaseInvoiceId && !empty($map[$purchaseInvoiceId])) ? $map[$purchaseInvoiceId] : null;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $item
     * @return mixed|null
     */
    private function getExpense(PurchaseInvoice $purchaseInvoice, array $item)
    {
        if (empty($item['expense_public_id'])) {
            return false;
        }

        $expense = Expense::scope($item['expense_public_id'])
            ->where('invoice_id', null)->firstOrFail();
        if (Auth::user()->can('edit', $expense)) {
            $expense->invoice_id = $purchaseInvoice->id;
            $expense->vendor_id = $purchaseInvoice->vendor_id;
            if ($expense->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $item
     * @return mixed|null
     */
    private function getTask(PurchaseInvoice $purchaseInvoice, array $item)
    {
        if (empty($item['task_public_id'])) {
            return false;
        }

        $task = Task::scope(trim($item['task_public_id']))
            ->whereNull('invoice_id')->firstOrFail();
        if (Auth::user()->can('edit', $task)) {
            $task->invoice_id = $purchaseInvoice->id;
            $task->vendor_id = $purchaseInvoice->vendor_id;
            if ($task->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $document_ids
     * @return mixed|null
     */
    private function uploadedPurchaseInvoiceDocuments(PurchaseInvoice $purchaseInvoice, array $document_ids)
    {
        if (empty($purchaseInvoice) || empty($document_ids)) {
            return false;
        }

        foreach ($document_ids as $document_id) {
            $document = Document::scope($document_id)->first();
            if ($document && Auth::user()->can('edit', $document)) {
                if ($document->invoice_id && $document->invoice_id != $purchaseInvoice->id) {
// From a clone
                    $document = $document->cloneDocument();
                    $document_ids[] = $document->public_id; // Don't remove this document
                }
                $document->invoice_id = $purchaseInvoice->id;
                $document->expense_id = null;
                $document->save();
            }
        }

        return true;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $document_ids
     * @return mixed|null
     */
    private function updatePurchaseInvoiceDocuments(PurchaseInvoice $purchaseInvoice, array $document_ids)
    {
        if (empty($purchaseInvoice) || empty($document_ids)) {
            return false;
        }
        if (!$purchaseInvoice->wasRecentlyCreated) {
            foreach ($purchaseInvoice->documents as $document) {
                if (!in_array($document->public_id, $document_ids)) {
                    if (Auth::user()->can('delete', $document)) {
// Not checking permissions; deleting a document is just editing the invoice
                        if ($document->invoice_id === $purchaseInvoice->id) {
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
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $origLineItems
     * @param array $newLineItem
     * @param bool $isNew
     * @return mixed|null
     */
    private function stockAdjustment($itemStore, PurchaseInvoice $purchaseInvoice, $origLineItems, array $newLineItem, $isNew)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }
        $qoh = !empty($itemStore) ? Utils::parseFloat($itemStore->qty) : 0;
        $demandQty = Utils::parseFloat(trim($newLineItem['qty']));

//        $purchase = Purchase::whereName($productKey);
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
     * @param PurchaseInvoice $purchaseInvoice
     * @param array $item
     * @return mixed|null
     */
    private function invoiceLineItemAdjustment($product, $itemStore, PurchaseInvoice $purchaseInvoice, array $item)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }
        $purchaseInvoicedQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $demandQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $itemCost = !empty($item['cost']) ? Utils::parseFloat(trim($item['cost'])) : 0;
        $purchaseInvoiceItem = PurchaseInvoiceItem::createNew($purchaseInvoice);
        $purchaseInvoiceItem->fill($item);
        $purchaseInvoiceItem->product_id = !empty($product) ? $product->id : null;
        $purchaseInvoiceItem->product_key = !empty($item['product_key']) ? trim($item['product_key']) : null;
        $purchaseInvoiceItem->notes = !empty($item['notes']) ? trim($item['notes']) : null;
        $purchaseInvoiceItem->cost = $itemCost;
        $purchaseInvoiceItem->qty = $purchaseInvoicedQty;
        $purchaseInvoiceItem->demand_qty = $demandQty;
        $purchaseInvoiceItem->discount = $purchaseInvoice->discount;
        $purchaseInvoiceItem->created_by = auth::user()->username;
        $qoh = !empty($itemStore->qty) ? Utils::parseFloat($itemStore->qty) : 0;
        if (!empty($itemStore) && $qoh < 1) {
            return false;
        }
        if ($purchaseInvoicedQty > $qoh) {
            $purchaseInvoiceItem->qty = $qoh;
        }

        if (!empty($item['custom_value1'])) {
            $purchaseInvoiceItem->custom_value1 = $item['custom_value1'];
        }
        if (!empty($item['custom_value2'])) {
            $purchaseInvoiceItem->custom_value2 = $item['custom_value2'];
        }
// provide backwards compatibility
        if (!empty($item['tax_name']) && !empty($item['tax_rate'])) {
            $item['tax_name1'] = $item['tax_name'];
            $item['tax_rate1'] = $item['tax_rate'];
        }

// provide backwards compatibility
        if (!empty($item['invoice_item_type_id']) && in_array($purchaseInvoiceItem->notes, [trans('texts.online_payment_surcharge'), trans('texts.online_payment_discount')])) {
            $purchaseInvoiceItem->invoice_item_type_id = $purchaseInvoice->balance > 0 ? INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE : INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
        }

        $purchaseInvoiceItem->fill($item);

        $purchaseInvoice->invoice_items()->save($purchaseInvoiceItem);

        return true;
    }

    /**
     * @param array $data
     * @param PurchaseInvoice $purchaseInvoice
     * @param $account
     * @return mixed|null
     */
    private function saveAccountDefault($account, PurchaseInvoice $purchaseInvoice, array $data)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        if ((!empty($data['set_default_terms']) && $data['set_default_terms'])
            || (!empty($data['set_default_footer']) && $data['set_default_footer'])) {
            if (!empty($data['set_default_terms']) && $data['set_default_terms']) {
                $account->{"{$purchaseInvoice->getEntityType()}_terms"} = trim($data['terms']);
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
     * @param PurchaseInvoice $purchaseInvoice
     * @return mixed|null
     */
    private function getLineItemNetTotal($account, PurchaseInvoice $purchaseInvoice, array $data)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

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
                        $purchaseInvoiceItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                        $purchaseInvoiceItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                        $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
//                 if quantity on hand greater than quantity demand
                        $qoh = Utils::roundSignificant(Utils::parseFloat($itemStore->qty));
                        if ($purchaseInvoiceItemQty > $qoh) {
                            $purchaseInvoiceItemQty = $qoh;
                        }
                        $total = $this->getLineItemTotal($purchaseInvoice, $purchaseInvoiceItemCost, $purchaseInvoiceItemQty, $discount, $total);
                    }
                } else {
                    $total = $this->getLineItemTotal($purchaseInvoice, trim($item['cost']), trim($item['qty']), trim($item['discount']), $total);
                }
            }
        }

        return $total;
    }

    /**
     * @param $account
     * @param array $data
     * @param PurchaseInvoice $purchaseInvoice
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemNetTax($account, PurchaseInvoice $purchaseInvoice, array $data, $total)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $itemTax = 0;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $itemStore = $this->getItemStore($account, $product);
                    if (!empty($itemStore)) {
                        $purchaseInvoiceItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                        $purchaseInvoiceItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                        $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                        $qoh = Utils::roundSignificant(Utils::parseFloat($itemStore->qty));
                        if ($purchaseInvoiceItemQty > $qoh) {
                            $purchaseInvoiceItemQty = $qoh;
                        }

                        $itemTax = $this->getLineItemTaxTotal($purchaseInvoice, $total, $purchaseInvoiceItemCost, $purchaseInvoiceItemQty, $item, $itemTax);
                    }
                } else {
                    $itemTax = $this->getLineItemTaxTotal($purchaseInvoice, $total, trim($item['cost']), trim($item['qty']), $item, $itemTax);
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
        if (empty($itemStore)) {
            return false;
        }

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
     * @param PurchaseInvoice $purchaseInvoice
     * @param float $purchaseInvoiceItemCost
     * @param float $purchaseInvoiceItemQty
     * @param $discount
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemTotal(PurchaseInvoice $purchaseInvoice, $purchaseInvoiceItemCost, $purchaseInvoiceItemQty, $discount, $total)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $discount = !empty($discount) ? Utils::parseFloat($discount) : 0;
        $lineTotal = floatval($purchaseInvoiceItemCost) * floatval($purchaseInvoiceItemQty);
        if ($discount) {
            if (!empty($purchaseInvoice->is_amount_discount)) {
                $lineTotal -= Utils::parseFloat($discount);
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }

        $total += round($lineTotal, 2);

        return $total;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     * @param float $total
     * @param float $purchaseInvoiceItemCost
     * @param float $purchaseInvoiceItemQty
     * @param array $item
     * @param float $itemTax
     * @return mixed|null
     */
    private function getLineItemTaxTotal(PurchaseInvoice $purchaseInvoice, $total, $purchaseInvoiceItemCost, $purchaseInvoiceItemQty, array $item, $itemTax)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $total = Utils::parseFloat($total);
        $itemTax = Utils::parseFloat($itemTax);
        $discount = !empty($item['discount']) ? round(Utils::parseFloat($item['discount']), 2) : 0;
        $lineTotal = floatval($purchaseInvoiceItemCost) * floatval($purchaseInvoiceItemQty);
        if ($discount) {
            if (!empty($purchaseInvoice->is_amount_discount)) {
                $lineTotal -= $discount;
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }
//          if any invoice discount
        $purchaseInvoiceDiscount = !empty($purchaseInvoice->discount) ? Utils::parseFloat($purchaseInvoice->discount) : 0;

        if ($purchaseInvoiceDiscount) {
            if (!empty($purchaseInvoice->is_amount_discount)) {
                if (!empty($total) && $total > 0) {
                    $lineTotal -= round($lineTotal / $total * $purchaseInvoiceDiscount, 4);
                }
            } else {
                $lineTotal -= round(($lineTotal * $purchaseInvoiceDiscount / 100), 4);
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
     * @param PurchaseInvoice $purchaseInvoice
     * @param $origLineItems
     * @param bool $isNew
     * @return mixed|null
     */
    private function getCalculatePurchaseInvoiceItem($account, PurchaseInvoice $purchaseInvoice, array $data, $origLineItems, $isNew)
    {
        if (empty($purchaseInvoice)) {
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
                    $this->getTask($purchaseInvoice, $item);
                }
                if (!empty($data['has_expenses'])) {
                    $this->getExpense($purchaseInvoice, $item);
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
//                  what if purchase_invoices, quotes, expenses and tasks
                        if (empty($data['is_quote'])) {
                            $this->stockAdjustment($itemStore, $purchaseInvoice, $origLineItems, $item, $isNew);
                        }
                        $this->invoiceLineItemAdjustment($product, $itemStore, $purchaseInvoice, $item);
                    }
                } else {
                    $this->invoiceLineItemAdjustment($product, $itemStore, $purchaseInvoice, $item);
                }
            }
        }

        return true;
    }

    /**
     * @param array $data
     * @param PurchaseInvoice $purchaseInvoice
     * @param float $total
     * @param $account
     * @param $itemTax
     * @param bool $publicId
     * @return mixed|null
     */
    private function getCalculatePurchaseInvoice($account, PurchaseInvoice $purchaseInvoice, array $data, $total, $itemTax, $publicId)
    {
        if (empty($purchaseInvoice)) {
            return false;
        }

        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $purchaseInvoiceDiscount = !empty($purchaseInvoice->discount) ? Utils::parseFloat($purchaseInvoice->discount) : 0;
//      if any invoice discount
        if ($purchaseInvoiceDiscount) {
            if (!empty($purchaseInvoice->is_amount_discount)) {
                $total -= $purchaseInvoiceDiscount;
            } else {
                $discount = round($total * ($purchaseInvoiceDiscount / 100), 2);
                $total -= $discount;
            }
        }

        if (!empty($data['custom_value1'])) {
            $purchaseInvoice->custom_value1 = round($data['custom_value1'], 2);
        }
        if (!empty($data['custom_value2'])) {
            $purchaseInvoice->custom_value2 = round($data['custom_value2'], 2);
        }

        if (!empty($data['custom_text_value1'])) {
            $purchaseInvoice->custom_text_value1 = trim($data['custom_text_value1']);
        }
        if (!empty($data['custom_text_value2'])) {
            $purchaseInvoice->custom_text_value2 = trim($data['custom_text_value2']);
        }

// custom fields charged taxes
        if ($purchaseInvoice->custom_value1 && $purchaseInvoice->custom_taxes1) {
            $total += $purchaseInvoice->custom_value1;
        }
        if ($purchaseInvoice->custom_value2 && $purchaseInvoice->custom_taxes2) {
            $total += $purchaseInvoice->custom_value2;
        }

        if (!empty($account->inclusive_taxes)) {
            $taxAmount1 = round($total * ($purchaseInvoice->tax_rate1 ? $purchaseInvoice->tax_rate1 : 0) / 100, 2);
            $taxAmount2 = round($total * ($purchaseInvoice->tax_rate2 ? $purchaseInvoice->tax_rate2 : 0) / 100, 2);

            $total = round($total + $taxAmount1 + $taxAmount2, 2);
            $total += $itemTax;
        }

// custom fields not charged taxes
        if ($purchaseInvoice->custom_value1 && !$purchaseInvoice->custom_taxes1) {
            $total += $purchaseInvoice->custom_value1;
        }
        if ($purchaseInvoice->custom_value2 && !$purchaseInvoice->custom_taxes2) {
            $total += $purchaseInvoice->custom_value2;
        }

        if (!empty($publicId)) {
            $purchaseInvoice->balance = round($total - ($purchaseInvoice->amount - $purchaseInvoice->balance), 2);
        } else {
            $purchaseInvoice->balance = $total;
        }

        if (!empty($data['partial'])) {
            $purchaseInvoice->partial = max(0, min(round(Utils::parseFloat($data['partial']), 2), $purchaseInvoice->balance));
        }

        if (!empty($purchaseInvoice->partial)) {
            if (!empty($data['partial_due_date'])) {
                $purchaseInvoice->partial_due_date = Utils::toSqlDate($data['partial_due_date']);
            }
        } else {
            $purchaseInvoice->partial_due_date = null;
        }

        $purchaseInvoice->amount = $total;

        $purchaseInvoice = $purchaseInvoice->save();

        return $purchaseInvoice;

    }


}
