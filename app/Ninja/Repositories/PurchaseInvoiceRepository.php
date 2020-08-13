<?php

namespace App\Ninja\Repositories;

use App\Events\BillItemsWereCreated;
use App\Events\BillItemsWereUpdated;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Jobs\SendBillEmail;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Document;
use App\Models\EntityModel;
use App\Models\Expense;
use App\Models\ItemStore;
use App\Models\BillInvitation;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Vendor;
use App\Services\PaymentService;
use Datatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillRepository extends BaseRepository
{
    protected $documentRepo;
    protected $model;
    protected $paymentService;
    protected $paymentRepo;

    public function __construct(
        Bill $model,
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
        return 'App\Models\Bill';
    }

    public function all()
    {
        return Bill::scope()
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
    public function getBills($accountId = false, $vendorPublicId = false, $entityType = ENTITY_BILL, $filter = false)
    {
        $query = DB::table('BILLs')
            ->LeftJoin('accounts', 'accounts.id', '=', 'BILLs.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'BILLs.vendor_id')
            ->leftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'BILLs.invoice_status_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('BILLs.account_id', $accountId)
            ->where('vendor_contacts.deleted_at', null)
            ->where('BILLs.is_recurring', false)
            ->where('vendor_contacts.is_primary', true)
//->whereRaw('(vendors.name != "" or vendor_contacts.first_name != "" or vendor_contacts.last_name != "" or vendor_contacts.email != "")') // filter out buy now BILLs
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                'vendors.user_id as vendor_user_id',
                'BILLs.invoice_number',
                'BILLs.invoice_number as quote_number',
                'BILLs.invoice_status_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'BILLs.public_id',
                'BILLs.amount',
                'BILLs.balance',
                'BILLs.discount',
                'BILLs.invoice_date',
                'BILLs.due_date as due_date_sql',
                'BILLs.partial_due_date',
                DB::raw("CONCAT(BILLs.invoice_date, BILLs.created_at) as date"),
                DB::raw("CONCAT(COALESCE(BILLs.partial_due_date, BILLs.due_date), BILLs.created_at) as due_date"),
                DB::raw("CONCAT(COALESCE(BILLs.partial_due_date, BILLs.due_date), BILLs.created_at) as valid_until"),
                'invoice_statuses.name as status',
                'invoice_statuses.name as invoice_status_name',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'BILLs.quote_id',
                'BILLs.quote_invoice_id',
                'BILLs.deleted_at',
                'BILLs.is_deleted',
                'BILLs.partial',
                'BILLs.user_id',
                'BILLs.is_public',
                'BILLs.is_recurring',
                'BILLs.private_notes',
                'BILLs.public_notes',
                'BILLs.created_at',
                'BILLs.updated_at',
                'BILLs.deleted_at',
                'BILLs.created_by',
                'BILLs.updated_by',
                'BILLs.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('BILLs.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('invoice_statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, $entityType, ENTITY_BILL);

        if ($statuses = session('entity_status_filter:' . $entityType)) {
            $statuses = explode(',', $statuses);
            $query->where(function ($query) use ($statuses) {
                foreach ($statuses as $status) {
                    if (in_array($status, EntityModel::$statuses)) {
                        continue;
                    }
                    $query->orWhere('BILLs.invoice_status_id', $status);
                }
                if (in_array(INVOICE_STATUS_UNPAID, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('BILLs.balance', '>', 0)
                            ->where('BILLs.is_public', true);
                    });
                }
                if (in_array(INVOICE_STATUS_OVERDUE, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('BILLs.balance', '>', 0)
                            ->where('BILLs.due_date', '<', date('Y-m-d'))
                            ->where('BILLs.is_public', true);
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
    public function getRecurringBills($accountId = false, $vendorPublicId = false, $filter = false)
    {
        $query = DB::table('BILLs')
            ->LeftJoin('accounts', 'accounts.id', '=', 'BILLs.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'BILLs.vendor_id')
            ->LeftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'BILLs.invoice_status_id')
            ->leftJoin('frequencies', 'frequencies.id', '=', 'BILLs.frequency_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('BILLs.account_id', $accountId)
            ->where('BILLs.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('vendor_contacts.deleted_at', null)
            ->where('BILLs.is_recurring', true)
            ->where('vendor_contacts.is_primary', true)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'BILLs.public_id',
                'BILLs.amount',
                'frequencies.name as frequency',
                'BILLs.start_date as start_date_sql',
                'BILLs.end_date as end_date_sql',
                'BILLs.last_sent_date as last_sent_date_sql',
                DB::raw("CONCAT(BILLs.start_date, BILLs.created_at) as start_date"),
                DB::raw("CONCAT(BILLs.end_date, BILLs.created_at) as end_date"),
                DB::raw("CONCAT(BILLs.last_sent_date, BILLs.created_at) as last_sent"),
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'BILLs.deleted_at',
                'BILLs.is_deleted',
                'BILLs.user_id',
                'invoice_statuses.name as invoice_status_name',
                'invoice_statuses.name as status',
                'BILLs.invoice_status_id',
                'BILLs.balance',
                'BILLs.due_date',
                'BILLs.due_date as due_date_sql',
                'BILLs.is_recurring',
                'BILLs.quote_invoice_id',
                'BILLs.public_notes',
                'BILLs.private_notes',
                'BILLs.created_at',
                'BILLs.updated_at',
                'BILLs.deleted_at',
                'BILLs.created_by',
                'BILLs.updated_by',
                'BILLs.deleted_by'
            );

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        } else {
            $query->where('vendors.deleted_at', null);
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('BILLs.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%');
            });
        }

//       don't remove the third parameter unless invoice and recurring invoice are separated
        $this->applyFilters($query, ENTITY_RECURRING_BILL, 'BILLs');

        return $query;
    }

    /**
     * @param $contactId
     * @param null $filter
     * @return mixed
     */
    public function getVendorRecurringDatatable($contactId, $filter = null)
    {
        $query = DB::table('invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'invitations.account_id')
            ->LeftJoin('BILLs', 'BILLs.id', '=', 'invitations.invoice_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'BILLs.vendor_id')
            ->LeftJoin('frequencies', 'frequencies.id', '=', 'BILLs.frequency_id')
            ->where('invitations.contact_id', $contactId)
            ->where('invitations.deleted_at', null)
            ->where('BILLs.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('BILLs.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('BILLs.is_recurring', true)
            ->where('BILLs.is_public', true)
            ->where('BILLs.deleted_at', null)
//->where('BILLs.start_date', '>=', date('Y-m-d H:i:s'))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'invitations.invitation_key',
                'BILLs.invoice_number',
                'BILLs.due_date',
                'vendors.public_id as vendor_public_id',
                'vendors.name as vendor_name',
                'BILLs.public_id',
                'BILLs.amount',
                'BILLs.start_date',
                'BILLs.end_date',
                'BILLs.auto_bill',
                'BILLs.client_enable_auto_bill',
                'frequencies.name as frequency',
                'BILLs.created_at',
                'BILLs.updated_at',
                'BILLs.deleted_at',
                'BILLs.created_by',
                'BILLs.updated_by',
                'BILLs.deleted_by'
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
    public function getVendorDatatable($contactId, $entityType, $search)
    {
        $query = DB::table('invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'invitations.account_id')
            ->LeftJoin('BILLs', 'BILLs.id', '=', 'invitations.invoice_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'BILLs.vendor_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('invitations.contact_id', $contactId)
            ->where('invitations.deleted_at', null)
            ->where('BILLs.invoice_type_id', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD)
            ->where('BILLs.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('vendor_contacts.deleted_at', null)
            ->where('vendor_contacts.is_primary', true)
            ->where('BILLs.is_recurring', false)
            ->where('BILLs.is_public', true)
// Only show paid BILLs for ninja accounts
//            ->whereRaw(sprintf("((accounts.account_key != '%s' and accounts.account_key not like '%s%%') or BILLs.invoice_status_id = %d)", env('NINJA_LICENSE_ACCOUNT_KEY'), substr(NINJA_ACCOUNT_KEY, 0, 30), INVOICE_STATUS_PAID))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'invitations.invitation_key',
                'BILLs.invoice_number',
                'BILLs.invoice_date',
                'BILLs.balance as balance',
                'BILLs.due_date',
                'BILLs.invoice_status_id',
                'BILLs.due_date',
                'BILLs.quote_invoice_id',
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'BILLs.public_id',
                'BILLs.amount',
                'BILLs.start_date',
                'BILLs.end_date',
                'BILLs.partial',
                'BILLs.created_at',
                'BILLs.updated_at',
                'BILLs.deleted_at',
                'BILLs.created_by',
                'BILLs.updated_by',
                'BILLs.deleted_by'
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

        if ($entityType == ENTITY_BILL) {
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
                } elseif (Bill::calcIsOverdue($model->balance, $model->due_date)) {
                    $class = 'danger';
                    if ($entityType == ENTITY_BILL) {
                        $label = trans('texts.past_due');
                    } else {
                        $label = trans('texts.expired');
                    }
                } else {
                    $class = 'default';
                    if ($entityType == ENTITY_BILL) {
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
     * @param Bill|null $Bill
     * @return Bill
     */
    public function save(array $data, Bill $Bill = null)
    {
        $account = $Bill ? $Bill->account : Auth::user()->account;
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $isNew = !$publicId || intval($publicId) < 0;

        if (!empty($Bill)) {
            $entityType = $Bill->getEntityType();
            $Bill->updated_by = Auth::user()->username;

        } elseif (!empty($isNew)) {
            $entityType = ENTITY_BILL;
            if (!empty($data['is_recurring']) && filter_var($data['is_recurring'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_RECURRING_BILL;
            } elseif (!empty($data['is_quote']) && filter_var($data['is_quote'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_QUOTE;
            }

            $Bill = $account->createBill($entityType, $data['client_id']);
            $Bill->invoice_date = date_create()->format('Y-m-d');
            $Bill->custom_taxes1 = $account->custom_invoice_taxes1 ?: false;
            $Bill->custom_taxes2 = $account->custom_invoice_taxes2 ?: false;
            $Bill->created_by = Auth::user()->username;
//           set the default due date
            if ($entityType === ENTITY_BILL && !empty($data['partial_due_date'])) {
                $vendor = Vendor::scope()->where('id', $data['client_id'])->first();
                $Bill->due_date = $account->defaultDueDate($vendor);
            }
        } else {
            $Bill = Bill::scope($publicId)->firstOrFail();
        }
        if (!empty($Bill->is_deleted)) {
            return $Bill;
        } elseif ($Bill->isLocked()) {
            return $Bill;
        }

//        if (isset($data['has_tasks']) && filter_var($data['has_tasks'], FILTER_VALIDATE_BOOLEAN)) {
//            $Bill->has_tasks = true;
//        }
        if (isset($data['has_expenses']) && filter_var($data['has_expenses'], FILTER_VALIDATE_BOOLEAN)) {
            $Bill->has_expenses = true;
        }

        if (isset($data['is_public']) && filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN)) {
            $Bill->is_public = true;
            if (!$Bill->isSent()) {
                $Bill->invoice_status_id = INVOICE_STATUS_SENT;
            }
        }

//     TODO: should be examine this expression
        if ($data['invoice_design_id'] && !$data['invoice_design_id']) {
            $data['invoice_design_id'] = 1;
        }

//      fill invoice
        $Bill->fill($data);

//      update account default template
//        $this->saveAccountDefault($account, $Bill, $data);

        if (!empty($data['invoice_number']) && !empty($Bill->is_recurring)) {
            $Bill->invoice_number = trim($data['invoice_number']);
        }

        if (isset($data['discount'])) {
            $Bill->discount = round(Utils::parseFloat($data['discount']), 2);
        }
        if (isset($data['is_amount_discount'])) {
            $Bill->is_amount_discount = $data['is_amount_discount'] ? true : false;
        }

        if (!empty($data['invoice_date_sql'])) {
            $Bill->invoice_date = $data['invoice_date_sql'];
        } elseif (!empty($data['invoice_date'])) {
            $Bill->invoice_date = Utils::toSqlDate($data['invoice_date']);
        }

        if (!empty($data['invoice_status_id'])) {
            if ($data['invoice_status_id'] == 0) {
                $data['invoice_status_id'] = INVOICE_STATUS_DRAFT;
            }
            $Bill->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        } else {
            $Bill->invoice_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        }
        if (!empty($Bill->is_recurring)) {
            if ($isNew && !empty($data['start_date']) && !empty($Bill->start_date)
                && $Bill->start_date != Utils::toSqlDate($data['start_date'])) {
                $Bill->last_sent_date = null;
            }

            $Bill->frequency_id = array_get($data, 'frequency_id', FREQUENCY_MONTHLY);
            $Bill->start_date = Utils::toSqlDate(array_get($data, 'start_date'));
            $Bill->end_date = Utils::toSqlDate(array_get($data, 'end_date'));
            $Bill->vendor_enable_auto_bill = !empty($data['client_enable_auto_bill']) && $data['client_enable_auto_bill'] ? true : false;
            $Bill->auto_bill = array_get($data, 'auto_bill_id') ?: array_get($data, 'auto_bill', AUTO_BILL_OFF);

            if ($Bill->auto_bill < AUTO_BILL_OFF || $Bill->auto_bill > AUTO_BILL_ALWAYS) {
                $Bill->auto_bill = AUTO_BILL_OFF;
            }

            if (!empty($data['recurring_due_date'])) {
                $Bill->due_date = $data['recurring_due_date'];
            } elseif (!empty($data['due_date'])) {
                $Bill->due_date = $data['due_date'];
            }
        } else {
            if ($isNew && empty($data['due_date']) && empty($data['due_date_sql'])) {
//           do nothing
            } elseif (!empty($data['due_date']) || !empty($data['due_date_sql'])) {
                $Bill->due_date = !empty($data['due_date_sql']) ? $data['due_date_sql'] :
                    Utils::toSqlDate($data['due_date']);
            }
//          invoice is not recurring
            $Bill->frequency_id = 0;
            $Bill->start_date = null;
            $Bill->end_date = null;
        }

        if (!empty($data['terms'])) {
            $Bill->terms = trim($data['terms']);
        } elseif ($isNew && !empty($Bill->is_recurring) && $account->{"{$entityType}_terms"}) {
            $Bill->terms = $account->{"{$entityType}_terms"};
        } else {
            $Bill->terms = '';
        }

        if (!empty($data['invoice_footer'])) {
            $Bill->invoice_footer = trim($data['invoice_footer']);
        } elseif ($isNew && !empty($Bill->is_recurring) && !empty($account->invoice_footer)) {
            $Bill->invoice_footer = $account->invoice_footer;
        } else {
            $Bill->invoice_footer = '';
        }

        $Bill->public_notes = !empty($data['public_notes']) ? trim($data['public_notes']) : '';

// process date variables if not recurring
        if (!empty($Bill->is_recurring)) {
            $Bill->terms = Utils::processVariables($Bill->terms);
            $Bill->invoice_footer = Utils::processVariables($Bill->invoice_footer);
            $Bill->public_notes = Utils::processVariables($Bill->public_notes);
        }

        if (!empty($data['po_number'])) {
            $Bill->po_number = trim($data['po_number']);
        }
//    provide backwards compatibility
        if (!empty($data['tax_name']) && !empty($data['tax_rate'])) {
            $data['tax_name1'] = $data['tax_name'];
            $data['tax_rate1'] = $data['tax_rate'];
        }

//       line item total
        $total = 0;
        $total = $this->getLineItemNetTotal($account, $Bill, $data);

//      line item tax
        $itemTax = 0;
        $itemTax = $this->getLineItemNetTax($account, $Bill, $data, $total);

//       save invoice
        $this->saveBillDetail($account, $Bill, $data, $total, $itemTax, $publicId);

        $origLineItems = [];
        if (!empty($publicId)) {
            $origLineItems = !empty($Bill->invoice_items) ? $Bill->invoice_items()->get()->toArray() : '';
//            remove old invoice line items
            $Bill->invoice_items()->forceDelete();
        }
//      update if any invoice documents
        if (!empty($data['document_ids'])) {
            $document_ids = array_map('intval', $data['document_ids']);
            $this->saveBillDocuments($Bill, $document_ids);
            $this->updateBillDocuments($Bill, $document_ids);

        }

//      purchase invoice line item detail
        $this->saveLineItemDetail($account, $Bill, $data);

        $this->saveInvitations($Bill);

//      finally dispatch events
        $this->dispatchEvents($Bill);

        return $Bill->load('invoice_items');
    }

    /**
     * @param $Bill
     * @return mixed
     */
    private function saveInvitations($Bill)
    {
        if (empty($Bill)) {
            return null;
        }

        $vendor = $Bill->vendor;

        $vendor->load('contacts');
        $sendBillIds = [];

        if (!$vendor->contacts->count()) {
            return $Bill;
        }

        foreach ($vendor->contacts as $contact) {
            if ($contact->send_invoice) {
                $sendBillIds[] = $contact->id;
            }
        }

        // if no contacts are selected auto-select the first to ensure there's an invitation
        if (!count($sendBillIds)) {
            $sendBillIds[] = $vendor->contacts[0]->id;
        }

        foreach ($vendor->contacts as $contact) {
            $invitation = BillInvitation::scope()->where('contact_id', $contact->id)
                ->where('invoice_id', $Bill->id)->first();
            if (in_array($contact->id, $sendBillIds) && empty($invitation)) {
                $invitation = BillInvitation::createNew($Bill);
                $invitation->invoice_id = $Bill->id;
                $invitation->contact_id = $contact->id;
                $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                $invitation->created_by = auth::user()->username;
                $invitation->save();
            } elseif (!in_array($contact->id, $sendBillIds) && !empty($invitation)) {
                $invitation->delete();
            }
        }

        if ($Bill->is_public && !$Bill->areInvitationsSent()) {
            $Bill->markInvitationsSent();
        }

        return $Bill;
    }

    /**
     * @param $Bill
     * @return null
     */
    private function dispatchEvents($Bill)
    {
        if (empty($Bill)) {
            return null;
        }
        if ($Bill->isType(INVOICE_TYPE_QUOTE)) {
            if ($Bill->wasRecentlyCreated) {
                event(new QuoteItemsWereCreated($Bill));
            } else {
                event(new QuoteItemsWereUpdated($Bill));
            }
        } else {
            if ($Bill->wasRecentlyCreated) {
                event(new BillItemsWereCreated($Bill));
            } else {
                event(new BillItemsWereUpdated($Bill));
            }
        }
    }

    /**
     * @param Bill $Bill
     * @param null $quoteId
     * @return mixed
     */
    public function cloneBill(Bill $Bill, $quoteId = null)
    {
        if (empty($Bill)) {
            return null;
        }

        $Bill->load('invitations', 'invoice_items');
        $account = $Bill->account;

        $clone = Bill::createNew($Bill);
        $clone->balance = $Bill->amount;

// if the invoice prefix is diff than quote prefix, use the same number for the invoice (if it's available)
        $BillNumber = false;
        if ($account->hasBillPrefix() && $account->share_counter) {
            $BillNumber = $Bill->invoice_number;
            if ($account->quote_number_prefix && strpos($BillNumber, $account->quote_number_prefix) === 0) {
                $BillNumber = substr($BillNumber, strlen($account->quote_number_prefix));
            }
            $BillNumber = $account->invoice_number_prefix . $BillNumber;
            $Bill = Bill::scope(false, $account->id)
                ->withTrashed()
                ->where('invoice_number', $BillNumber)
                ->first();
            if ($Bill) {
                $BillNumber = false;
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
            $clone->$field = $Bill->$field;
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

        $clone->invoice_number = $BillNumber ?: $account->getVendorNextNumber($clone);
        $clone->invoice_date = date_create()->format('Y-m-d');
        $clone->due_date = $account->defaultDueDate($Bill->vendor);
        $clone->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
        $clone->save();

        if ($quoteId) {
            $Bill->invoice_status_id = !empty($clone->invoice_status_id) ? $clone->invoice_status_id : INVOICE_STATUS_DRAFT;
            $Bill->quote_invoice_id = $clone->public_id;
            $Bill->save();
        }

        foreach ($Bill->invoice_items as $item) {
//          invoice item instance
            $cloneItem = BillItem::createNew($Bill);
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

        foreach ($Bill->documents as $document) {
            $cloneDocument = $document->cloneDocument();
            $clone->documents()->save($cloneDocument);
        }

        foreach ($Bill->invitations as $invitation) {
            $cloneInvitation = BillInvitation::createNew($Bill);
            $cloneInvitation->contact_id = $invitation->contact_id;
            $cloneInvitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $clone->invitations()->save($cloneInvitation);
        }

        $this->dispatchEvents($clone);

        return $clone;
    }

    /**
     * @param Bill $Bill
     * @return mixed|null
     */
    public function emailBill(Bill $Bill)
    {
        if (empty($Bill)) {
            return null;
        }

        if (config('queue.default') === 'sync') {
            app('App\Ninja\Mailers\ContactMailer')->sendBill($Bill);
        } else {
            dispatch(new SendBillEmail($Bill));
        }
    }

    /**
     * @param Bill $Bill
     */
    public function markSent(Bill $Bill)
    {
        $Bill->markSent();
    }

    /**
     * @param Bill $Bill
     * @return mixed|void|null
     */
    public function markPaid(Bill $Bill)
    {
        if (!$Bill->canBePaid()) {
            return null;
        }

        $Bill->markSentIfUnsent();

        $data = [
            'vendor_id' => $Bill->vendor_id,
            'invoice_id' => $Bill->id,
            'amount' => $Bill->balance,
        ];

        return $this->paymentRepo->save($data);
    }

    /**
     * @param $invitationKey
     * @return Invitation|mixed
     */
    public function findBillByInvitation($invitationKey)
    {
        if (empty($invitationKey)) {
            return null;
        }
// check for extra params at end of value (from website feature)
        list($invitationKey) = explode('&', $invitationKey);
        $invitationKey = substr($invitationKey, 0, RANDOM_KEY_LENGTH);

        $invitation = BillInvitation::where('invitation_key', $invitationKey)->first();

        if (empty($invitation)) {
            return false;
        }

        $Bill = $invitation->invoice;
        if (empty($Bill) || isset($Bill->is_deleted)) {
            return false;
        }

        $Bill->load('user', 'invoice_items', 'documents', 'invoice_design', 'account.country', 'vendor.contacts', 'vendor.country');
        $vendor = $Bill->vendor;

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

        return !empty($product) ? $product : false;
    }

    /**
     * @param $account
     * @param null $product
     * @return mixed
     */
    public function getItemStore($account, $product = null)
    {
        if (empty($account) || empty($product)) {
            return false;
        }

        $warehouseId = !empty(auth::user()->branch->warehouse_id) ? auth::user()->branch->warehouse_id : null;

        $itemStore = ItemStore::scope()
            ->where('account_id', $account->id)
            ->where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->where('deleted_at', null)
            ->first();

        return !empty($itemStore) ? $itemStore : false;
    }

    /**
     * @param $vendorId
     * @return mixed
     */
    public function findOpenBills($vendorId)
    {
        if (empty($vendorId)) {
            return false;
        }
        $query = Bill::scope()
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
     * @param Bill $recurBill
     * @return mixed
     */
    public function createRecurringBill(Bill $recurBill)
    {
        if (empty($recurBill)) {
            return null;
        }

        $recurBill->load('account.timezone', 'invoice_items', 'vendor', 'user');
        $vendor = $recurBill->vendor;

        if ($vendor->deleted_at) {
            return false;
        }

        if (!isset($recurBill->user->confirmed)) {
            return false;
        }

        if (!$recurBill->shouldSendToday()) {
            return false;
        }

        $Bill = Bill::createNew($recurBill);
        $Bill->is_public = true;
        $Bill->invoice_type_id = INVOICE_TYPE_STANDARD;
        $Bill->vendor_id = $recurBill->vendor_id;
        $Bill->recurring_invoice_id = $recurBill->id;
        $Bill->invoice_number = $recurBill->account->getVendorNextNumber($Bill);
        $Bill->amount = $recurBill->amount;
        $Bill->balance = $recurBill->amount;
        $Bill->invoice_date = date_create()->format('Y-m-d');
        $Bill->discount = $recurBill->discount;
        $Bill->po_number = $recurBill->po_number;
        $Bill->public_notes = Utils::processVariables($recurBill->public_notes, $vendor);
        $Bill->terms = Utils::processVariables($recurBill->terms ?: $recurBill->account->invoice_terms, $vendor);
        $Bill->invoice_footer = Utils::processVariables($recurBill->invoice_footer ?: $recurBill->account->invoice_footer, $vendor);
        $Bill->tax_name1 = $recurBill->tax_name1;
        $Bill->tax_rate1 = $recurBill->tax_rate1;
        $Bill->tax_name2 = $recurBill->tax_name2;
        $Bill->tax_rate2 = $recurBill->tax_rate2;
        $Bill->invoice_design_id = $recurBill->invoice_design_id;
        $Bill->custom_value1 = $recurBill->custom_value1 ?: 0;
        $Bill->custom_value2 = $recurBill->custom_value2 ?: 0;
        $Bill->custom_taxes1 = $recurBill->custom_taxes1 ?: 0;
        $Bill->custom_taxes2 = $recurBill->custom_taxes2 ?: 0;
        $Bill->custom_text_value1 = Utils::processVariables($recurBill->custom_text_value1, $vendor);
        $Bill->custom_text_value2 = Utils::processVariables($recurBill->custom_text_value2, $vendor);
        $Bill->is_amount_discount = $recurBill->is_amount_discount;
        $Bill->due_date = $recurBill->getDueDate();
        $Bill->save();

        foreach ($recurBill->invoice_items as $recurItem) {
            $item = BillItem::createNew($recurItem);
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

            $Bill->invoice_items()->save($item);
        }

        foreach ($recurBill->documents as $recurDocument) {
            $document = $recurDocument->cloneDocument();
            $Bill->documents()->save($document);
        }

        foreach ($recurBill->invitations as $recurInvitation) {
            $invitation = BillInvitation::createNew($recurInvitation);
            $invitation->contact_id = $recurInvitation->contact_id;
            $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $Bill->invitations()->save($invitation);
        }

        $recurBill->last_sent_date = date('Y-m-d');
        $recurBill->save();

        if ($recurBill->getAutoBillEnabled() && !$recurBill->account->auto_bill_on_due_date) {
// autoBillBill will check for ACH, so we're not checking here
            if ($this->paymentService->autoBillBill($Bill)) {
// update the invoice reference to match its actual state
// this is to ensure a 'payment received' email is sent
                $Bill->invoice_status_id = INVOICE_STATUS_PAID;
            }
        }

        $this->dispatchEvents($Bill);

        return $Bill;
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
        $BILLs = Bill::invoiceType(INVOICE_TYPE_STANDARD)
            ->with('vendor', 'invoice_items')
            ->whereHas('vendor', function ($query) {
                $query->where('send_reminders', true);
            })
            ->where('account_id', $account->id)
            ->where('balance', '>', 0)
            ->where('is_recurring', false)
            ->where('is_public', true)
            ->whereRaw('(' . $sql . ')')
            ->get();

        return $BILLs;
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

        $BILLs = Bill::invoiceType(INVOICE_TYPE_STANDARD)
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
            $BILLs->where($field, '<', $date);
        }

        return $BILLs->get();
    }

    /**
     * @param $Bill
     * @return mixed|null
     */
    public function clearGatewayFee($Bill)
    {
        if (empty($Bill)) {
            return false;
        }

        $account = $Bill->account;

        if (!$Bill->relationLoaded('invoice_items')) {
            $Bill->load('invoice_items');
        }

        $data = $Bill->toArray();
        foreach ($data['invoice_items'] as $key => $item) {
            if ($item['invoice_item_type_id'] == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
                unset($data['invoice_items'][$key]);
                $this->save($data, $Bill);
                break;
            }
        }

        return true;
    }

    /**
     * @param $Bill
     * @param $amount
     * @param $percent
     * @return mixed|null
     */
    public function setLateFee($Bill, $amount, $percent)
    {
        if (empty($Bill)) {
            return false;
        }

        if ($amount <= 0 && $percent <= 0) {
            return false;
        }

        $account = $Bill->account;

        $data = $Bill->toArray();
        $fee = $amount;

        if ($Bill->getRequestedAmount() > 0) {
            $fee += round($Bill->getRequestedAmount() * $percent / 100, 2);
        }

        $item = [];
        $item['product_key'] = trans('texts.fee');
        $item['notes'] = trans('texts.late_fee_added', ['date' => $account->formatDate('now')]);
        $item['qty'] = 1;
        $item['cost'] = $fee;
        $item['invoice_item_type_id'] = INVOICE_ITEM_TYPE_LATE_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $Bill);

        return true;
    }

    /**
     * @param $Bill
     * @param $gatewayTypeId
     * @return mixed|null
     */
    public function setGatewayFee($Bill, $gatewayTypeId)
    {
        if (empty($Bill)) {
            return false;
        }

        $account = $Bill->account;

        if (!isset($account->gateway_fee_enabled)) {
            return false;
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);
        $this->clearGatewayFee($Bill);

        if (empty($settings)) {
            return false;
        }

        $data = $Bill->toArray();
        $fee = $Bill->calcGatewayFee($gatewayTypeId);
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

        $this->save($data, $Bill);

        return true;
    }

    /**
     * @param $BillNumber
     * @return mixed|null
     */
    public function findPhonetically($BillNumber)
    {
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $BillId = 0;

        $BILLs = Bill::scope()->get(['id', 'invoice_number', 'public_id']);

        foreach ($BILLs as $Bill) {
            $map[$Bill->id] = $Bill;
            $similar = similar_text($BillNumber, $Bill->invoice_number, $percent);
            if ($percent > $max) {
                $BillId = $Bill->id;
                $max = $percent;
            }
        }

        return ($BillId && !empty($map[$BillId])) ? $map[$BillId] : null;
    }

    /**
     * @param Bill $Bill
     * @param array $item
     * @return mixed|null
     */
    private function saveExpense(Bill $Bill, array $item)
    {
        if (empty($item['expense_public_id'])) {
            return false;
        }

        $expense = Expense::scope($item['expense_public_id'])
            ->where('invoice_id', null)->firstOrFail();
        if (Auth::user()->can('edit', $expense)) {
            $expense->invoice_id = $Bill->id;
            $expense->vendor_id = $Bill->vendor_id;
            if ($expense->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Bill $Bill
     * @param array $item
     * @return mixed|null
     */
//    private function getTask(Bill $Bill, array $item)
//    {
//        if (empty($item['task_public_id'])) {
//            return false;
//        }
//
//        $task = Task::scope(trim($item['task_public_id']))
//            ->whereNull('invoice_id')->firstOrFail();
//        if (Auth::user()->can('edit', $task)) {
//            $task->invoice_id = $Bill->id;
//            $task->vendor_id = $Bill->vendor_id;
//            if ($task->save()) {
//                return true;
//            }
//        }
//
//        return false;
//    }

    /**
     * @param Bill $Bill
     * @param array $document_ids
     * @return mixed|null
     */
    private function saveBillDocuments(Bill $Bill, array $document_ids)
    {
        if (empty($Bill) || empty($document_ids)) {
            return false;
        }

        foreach ($document_ids as $document_id) {
            $document = Document::scope($document_id)->first();
            if ($document && Auth::user()->can('edit', $document)) {
                if ($document->invoice_id && $document->invoice_id != $Bill->id) {
//                From a clone
                    $document = $document->cloneDocument();
                    $document_ids[] = $document->public_id; // Don't remove this document
                }
                $document->invoice_id = $Bill->id;
                $document->expense_id = null;
                $document->save();
            }
        }

        return true;
    }

    /**
     * @param Bill $Bill
     * @param array $document_ids
     * @return mixed|null
     */
    private function updateBillDocuments(Bill $Bill, array $document_ids)
    {
        if (empty($Bill) || empty($document_ids)) {
            return false;
        }
        if (!$Bill->wasRecentlyCreated) {
            foreach ($Bill->documents as $document) {
                if (!in_array($document->public_id, $document_ids)) {
                    if (Auth::user()->can('delete', $document)) {
// Not checking permissions; deleting a document is just editing the invoice
                        if ($document->invoice_id === $Bill->id) {
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
     * @param Bill $Bill
     * @param array $origLineItems
     * @param array $newLineItem
     * @param bool $isNew
     * @return mixed|null
     */
    private function stockAdjustment($itemStore, Bill $Bill, $origLineItems, array $newLineItem, $isNew)
    {
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
     * @param Bill $Bill
     * @param array $item
     * @return mixed|null
     */
    private function saveInvoiceLineItemAdjustment($product, $itemStore, Bill $Bill, array $item)
    {
        $BilldQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $demandQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $itemCost = !empty($item['cost']) ? Utils::parseFloat(trim($item['cost'])) : (isset($product->cost) ? $product->cost : 0);
        $BillItem = BillItem::createNew($Bill);
        $BillItem->fill($item);
        $BillItem->product_id = !empty($product->id) ? $product->id : null;
        $BillItem->product_key = !empty($item['product_key']) ? trim($item['product_key']) : null;
        $BillItem->notes = !empty($item['notes']) ? trim($item['notes']) : null;
        $BillItem->cost = $itemCost;
        $BillItem->qty = $BilldQty;
        $BillItem->demand_qty = $demandQty;
        $BillItem->discount = $Bill->discount;
        $BillItem->created_by = auth::user()->username;

        if (!empty($item['custom_value1'])) {
            $BillItem->custom_value1 = $item['custom_value1'];
        }
        if (!empty($item['custom_value2'])) {
            $BillItem->custom_value2 = $item['custom_value2'];
        }
// provide backwards compatibility
        if (!empty($item['tax_name']) && !empty($item['tax_rate'])) {
            $item['tax_name1'] = $item['tax_name'];
            $item['tax_rate1'] = $item['tax_rate'];
        }

// provide backwards compatibility
        if (!empty($item['invoice_item_type_id']) && in_array($BillItem->notes, [trans('texts.online_payment_surcharge'), trans('texts.online_payment_discount')])) {
            $BillItem->invoice_item_type_id = $Bill->balance > 0 ? INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE : INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
        }

        $BillItem->fill($item);

        $Bill->invoice_items()->save($BillItem);

        return true;
    }

    /**
     * @param array $data
     * @param Bill $Bill
     * @param $account
     * @return mixed|null
     */
//    private function saveAccountDefault($account, Bill $Bill, array $data)
//    {
//        if (empty($Bill)) {
//            return false;
//        }
//
//        if ((!empty($data['set_default_terms']) && $data['set_default_terms'])
//            || (!empty($data['set_default_footer']) && $data['set_default_footer'])) {
//            if (!empty($data['set_default_terms']) && $data['set_default_terms']) {
//                $account->{"{$Bill->getEntityType()}_terms"} = trim($data['terms']);
//            }
//            if (!empty($data['set_default_footer']) && $data['set_default_footer']) {
//                $account->invoice_footer = trim($data['invoice_footer']);
//            }
//
//            $account->save();
//        }
//
//        return true;
//    }

    /**
     * @param $account
     * @param array $data
     * @param Bill $Bill
     * @return mixed|null
     */
    private function getLineItemNetTotal($account, Bill $Bill, array $data)
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
                    $BillItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                    $BillItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                    $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                    $total = $this->getLineItemTotal($Bill, $BillItemCost, $BillItemQty, $discount, $total);
                } else {
                    $total = $this->getLineItemTotal($Bill, trim($item['cost']), trim($item['qty']), trim($item['discount']), $total);
                }
            }
        }

        return $total;
    }

    /**
     * @param $account
     * @param array $data
     * @param Bill $Bill
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemNetTax($account, Bill $Bill, array $data, $total)
    {
        $itemTax = 0;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $BillItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : (isset($product->cost) ? $product->cost : 0);
                    $BillItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                    $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                    $itemTax = $this->getLineItemTaxTotal($Bill, $total, $BillItemCost, $BillItemQty, $item, $itemTax);
                } else {
                    $itemTax = $this->getLineItemTaxTotal($Bill, $total, trim($item['cost']), trim($item['qty']), $item, $itemTax);
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
     * @param Bill $Bill
     * @param float $BillItemCost
     * @param float $BillItemQty
     * @param $discount
     * @param float $total
     * @return mixed|null
     */
    private function getLineItemTotal(Bill $Bill, $BillItemCost, $BillItemQty, $discount, $total)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $discount = !empty($discount) ? Utils::parseFloat($discount) : 0;
        $lineTotal = floatval($BillItemCost) * floatval($BillItemQty);
        if ($discount) {
            if (!empty($Bill->is_amount_discount)) {
                $lineTotal -= Utils::parseFloat($discount);
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }

        $total += round($lineTotal, 2);

        return $total;
    }

    /**
     * @param Bill $Bill
     * @param float $total
     * @param float $BillItemCost
     * @param float $BillItemQty
     * @param array $item
     * @param float $itemTax
     * @return mixed|null
     */
    private function getLineItemTaxTotal(Bill $Bill, $total, $BillItemCost, $BillItemQty, array $item, $itemTax)
    {
        $total = Utils::parseFloat($total);
        $itemTax = Utils::parseFloat($itemTax);
        $discount = !empty($item['discount']) ? round(Utils::parseFloat($item['discount']), 2) : 0;
        $lineTotal = floatval($BillItemCost) * floatval($BillItemQty);
        if ($discount) {
            if (!empty($Bill->is_amount_discount)) {
                $lineTotal -= $discount;
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }
//          if any invoice discount
        $BillDiscount = !empty($Bill->discount) ? Utils::parseFloat($Bill->discount) : 0;

        if ($BillDiscount) {
            if (!empty($Bill->is_amount_discount)) {
                if (!empty($total) && $total > 0) {
                    $lineTotal -= round($lineTotal / $total * $BillDiscount, 4);
                }
            } else {
                $lineTotal -= round(($lineTotal * $BillDiscount / 100), 4);
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
     * @param Bill $Bill
     * @param array $data
     * @return mixed|null
     */
    private function saveLineItemDetail($account, Bill $Bill, array $data)
    {
        if (empty($Bill)) {
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
                if (!empty($data['has_expenses'])) {
                    $this->saveExpense($Bill, $item);
                }
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $itemStore = $this->getItemStore($account, $product);
                    $this->saveInvoiceLineItemAdjustment($product, $itemStore, $Bill, $item);
                }
            }
            return true;
        }
    }

    /**
     * @param array $data
     * @param Bill $Bill
     * @param float $total
     * @param $account
     * @param $itemTax
     * @param bool $publicId
     * @return mixed|null
     */
    private function saveBillDetail($account, Bill $Bill, array $data, $total, $itemTax, $publicId)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $BillDiscount = !empty($Bill->discount) ? Utils::parseFloat($Bill->discount) : 0;
//      if any invoice discount
        if ($BillDiscount) {
            if (!empty($Bill->is_amount_discount)) {
                $total -= $BillDiscount;
            } else {
                $discount = round($total * ($BillDiscount / 100), 2);
                $total -= $discount;
            }
        }

        if (!empty($data['custom_value1'])) {
            $Bill->custom_value1 = round($data['custom_value1'], 2);
        }
        if (!empty($data['custom_value2'])) {
            $Bill->custom_value2 = round($data['custom_value2'], 2);
        }

        if (!empty($data['custom_text_value1'])) {
            $Bill->custom_text_value1 = trim($data['custom_text_value1']);
        }
        if (!empty($data['custom_text_value2'])) {
            $Bill->custom_text_value2 = trim($data['custom_text_value2']);
        }

// custom fields charged taxes
        if ($Bill->custom_value1 && $Bill->custom_taxes1) {
            $total += $Bill->custom_value1;
        }
        if ($Bill->custom_value2 && $Bill->custom_taxes2) {
            $total += $Bill->custom_value2;
        }

        if (!empty($account->inclusive_taxes)) {
            $taxAmount1 = round($total * ($Bill->tax_rate1 ? $Bill->tax_rate1 : 0) / 100, 2);
            $taxAmount2 = round($total * ($Bill->tax_rate2 ? $Bill->tax_rate2 : 0) / 100, 2);

            $total = round($total + $taxAmount1 + $taxAmount2, 2);
            $total += $itemTax;
        }

// custom fields not charged taxes
        if ($Bill->custom_value1 && !$Bill->custom_taxes1) {
            $total += $Bill->custom_value1;
        }
        if ($Bill->custom_value2 && !$Bill->custom_taxes2) {
            $total += $Bill->custom_value2;
        }

        if (!empty($publicId)) {
            $Bill->balance = round($total - ($Bill->amount - $Bill->balance), 2);
        } else {
            $Bill->balance = $total;
        }

        if (!empty($data['partial'])) {
            $Bill->partial = max(0, min(round(Utils::parseFloat($data['partial']), 2), $Bill->balance));
        }

        if (!empty($Bill->partial)) {
            if (!empty($data['partial_due_date'])) {
                $Bill->partial_due_date = Utils::toSqlDate($data['partial_due_date']);
            }
        } else {
            $Bill->partial_due_date = null;
        }

        $Bill->amount = $total;

        $Bill = $Bill->save();

        return $Bill;
    }

}
