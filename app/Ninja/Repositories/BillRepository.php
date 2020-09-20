<?php

namespace App\Ninja\Repositories;

use App\Events\Purchase\BillItemsWereCreatedEvent;
use App\Events\Purchase\BillItemsWereUpdatedEvent;
use App\Events\Purchase\BillQuoteItemsWereCreatedEvent;
use App\Events\Purchase\BillQuoteItemsWereUpdatedEvent;
use App\Jobs\SendBillEmail;
use App\Libraries\Utils;
use App\Models\Common\Account;
use App\Models\Bill;
use App\Models\BillInvitation;
use App\Models\BillItem;
use App\Models\Document;
use App\Models\EntityModel;
use App\Models\Expense;
use App\Models\ItemStore;
use App\Models\Vendor;
use App\Services\BillPaymentService;
use Datatable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class BillRepository extends BaseRepository
{
    protected $documentRepo;
    protected $model;
    protected $paymentService;
    protected $paymentRepo;

    /**
     * BillRepository constructor.
     * @param Bill $model
     * @param BillPaymentService $paymentService
     * @param BillPaymentRepository $paymentRepo
     * @param DocumentRepository $documentRepo
     */
    public function __construct(Bill $model, BillPaymentService $paymentService, BillPaymentRepository $paymentRepo, DocumentRepository $documentRepo)
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
        return Bill::scope()->billType(BILL_TYPE_STANDARD)
            ->with('user', 'vendor.contacts', 'invoice_status')
            ->withTrashed()->where('is_recurring', false)
            ->get();
    }

    public function getBills($accountId = false, $vendorPublicId = false, $entityType = ENTITY_BILL, $filter = false)
    {
        $query = DB::table('bills')
            ->LeftJoin('accounts', 'accounts.id', '=', 'bills.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'bills.vendor_id')
            ->leftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'bills.bill_status_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('bills.account_id', $accountId)
//            ->where('vendor_contacts.deleted_at', null)
            ->where('bills.is_recurring', false)
            ->where('vendor_contacts.is_primary', true)
//->whereRaw('(vendors.name != "" or vendor_contacts.first_name != "" or vendor_contacts.last_name != "" or vendor_contacts.email != "")') // filter out buy now bills
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                'vendors.user_id as vendor_user_id',
                'bills.invoice_number',
                'bills.invoice_number as quote_number',
                'bills.bill_status_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'bills.public_id',
                'bills.amount',
                'bills.balance',
                'bills.discount',
                'bills.bill_date',
                'bills.due_date as due_date_sql',
                'bills.partial_due_date',
                DB::raw("CONCAT(bills.bill_date, bills.created_at) as date"),
                DB::raw("CONCAT(COALESCE(bills.partial_due_date, bills.due_date), bills.created_at) as due_date"),
                DB::raw("CONCAT(COALESCE(bills.partial_due_date, bills.due_date), bills.created_at) as valid_until"),
                'invoice_statuses.name as status',
                'invoice_statuses.name as bill_status_name',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'bills.quote_id',
                'bills.quote_bill_id',
                'bills.deleted_at',
                'bills.is_deleted',
                'bills.partial',
                'bills.user_id',
                'bills.is_public',
                'bills.is_recurring',
                'bills.private_notes',
                'bills.public_notes',
                'bills.created_at',
                'bills.updated_at',
                'bills.deleted_at',
                'bills.created_by',
                'bills.updated_by',
                'bills.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('bills.invoice_number', 'like', '%' . $filter . '%')
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
                    $query->orWhere('bills.bill_status_id', $status);
                }
                if (in_array(BILL_STATUS_UNPAID, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('bills.balance', '>', 0)
                            ->where('bills.is_public', true);
                    });
                }
                if (in_array(BILL_STATUS_OVERDUE, $statuses)) {
                    $query->orWhere(function ($query) use ($statuses) {
                        $query->where('bills.balance', '>', 0)
                            ->where('bills.due_date', '<', date('Y-m-d'))
                            ->where('bills.is_public', true);
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

    public function getRecurringBills($accountId, $vendorPublicId, $entityType, $filter = false)
    {
        $query = DB::table('bills')
            ->LeftJoin('accounts', 'accounts.id', '=', 'bills.account_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'bills.vendor_id')
            ->LeftJoin('invoice_statuses', 'invoice_statuses.id', '=', 'bills.bill_status_id')
            ->leftJoin('frequencies', 'frequencies.id', '=', 'bills.frequency_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('bills.account_id', $accountId)
            ->where('bills.bill_type_id', BILL_TYPE_STANDARD)
//            ->where('vendor_contacts.deleted_at', null)
            ->where('bills.is_recurring', true)
            ->where('vendor_contacts.is_primary', true)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'bills.public_id',
                'bills.amount',
                'frequencies.name as frequency',
                'bills.start_date as start_date_sql',
                'bills.end_date as end_date_sql',
                'bills.last_sent_date as last_sent_date_sql',
                DB::raw("CONCAT(bills.start_date, bills.created_at) as start_date"),
                DB::raw("CONCAT(bills.end_date, bills.created_at) as end_date"),
                DB::raw("CONCAT(bills.last_sent_date, bills.created_at) as last_sent"),
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'bills.deleted_at',
                'bills.is_deleted',
                'bills.user_id',
                'bills.bill_status_id',
                'invoice_statuses.name as bill_status_name',
                'bills.balance',
                'bills.due_date',
                'bills.due_date as due_date_sql',
                'bills.is_recurring',
                'bills.quote_bill_id',
                'bills.public_notes',
                'bills.private_notes',
                'bills.created_at',
                'bills.updated_at',
                'bills.deleted_at',
                'bills.created_by',
                'bills.updated_by',
                'bills.deleted_by'
            );

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        } else {
            $query->where('vendors.deleted_at', null);
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('bills.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%');
            });
        }

//       don't remove the third parameter unless bill and recurring bill are separated
        $this->applyFilters($query, $entityType, 'bill');

        return $query;
    }

    public function getVendorRecurringDatatable($contactId, $filter = null)
    {
        $query = DB::table('bill_invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'bill_invitations.account_id')
            ->LeftJoin('bills', 'bills.id', '=', 'bill_invitations.bill_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'bills.vendor_id')
            ->LeftJoin('frequencies', 'frequencies.id', '=', 'bills.frequency_id')
            ->where('bill_invitations.contact_id', $contactId)
//            ->where('bill_invitations.deleted_at', null)
            ->where('bills.bill_type_id', BILL_TYPE_STANDARD)
            ->where('bills.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('bills.is_recurring', true)
            ->where('bills.is_public', true)
            ->where('bills.deleted_at', null)
//->where('bills.start_date', '>=', date('Y-m-d H:i:s'))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'bill_invitations.invitation_key',
                'bills.invoice_number',
                'bills.due_date',
                'vendors.public_id as vendor_public_id',
                'vendors.name as vendor_name',
                'bills.public_id',
                'bills.amount',
                'bills.start_date',
                'bills.end_date',
                'bills.auto_bill',
                'bills.client_enable_auto_bill',
                'frequencies.name as frequency',
                'bills.created_at',
                'bills.updated_at',
                'bills.deleted_at',
                'bills.created_by',
                'bills.updated_by',
                'bills.deleted_by'
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

    public function getVendorDatatable($contactId, $entityType, $search)
    {
        $query = DB::table('bill_invitations')
            ->LeftJoin('accounts', 'accounts.id', '=', 'bill_invitations.account_id')
            ->LeftJoin('bills', 'bills.id', '=', 'bill_invitations.bill_id')
            ->LeftJoin('vendors', 'vendors.id', '=', 'bills.vendor_id')
            ->LeftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('bill_invitations.contact_id', $contactId)
            ->where('bill_invitations.deleted_at', null)
            ->where('bills.bill_type_id', $entityType == ENTITY_BILL_QUOTE ? BILL_TYPE_QUOTE : BILL_TYPE_STANDARD)
            ->where('bills.is_deleted', false)
            ->where('vendors.deleted_at', null)
            ->where('vendor_contacts.deleted_at', null)
            ->where('vendor_contacts.is_primary', true)
            ->where('bills.is_recurring', false)
            ->where('bills.is_public', true)
// Only show paid bills for ninja accounts
//            ->whereRaw(sprintf("((accounts.account_key != '%s' and accounts.account_key not like '%s%%') or bills.bill_status_id = %d)", env('NINJA_LICENSE_ACCOUNT_KEY'), substr(NINJA_ACCOUNT_KEY, 0, 30), BILL_STATUS_PAID))
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'bill_invitations.invitation_key',
                'bills.invoice_number',
                'bills.bill_date',
                'bills.balance as balance',
                'bills.due_date',
                'bills.bill_status_id',
                'bills.due_date',
                'bills.quote_bill_id',
                'vendors.public_id as vendor_public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'bills.public_id',
                'bills.amount',
                'bills.start_date',
                'bills.end_date',
                'bills.partial',
                'bills.created_at',
                'bills.updated_at',
                'bills.deleted_at',
                'bills.created_by',
                'bills.updated_by',
                'bills.deleted_by'
            );

        $table = Datatable::query($query)
            ->addColumn('invoice_number', function ($model) use ($entityType) {
                return link_to('/vendor/view/' . $model->invitation_key, $model->invoice_number)->toHtml();
            })
            ->addColumn('bill_date', function ($model) {
                return Utils::fromSqlDate($model->bill_date);
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
            ->addColumn('bill_status_id', function ($model) use ($entityType) {
                if ($model->bill_status_id == BILL_STATUS_PAID) {
                    $label = trans('texts.status_paid');
                    $class = 'success';
                } elseif ($model->bill_status_id == BILL_STATUS_PARTIAL) {
                    $label = trans('texts.status_partial');
                    $class = 'info';
                } elseif ($entityType == ENTITY_BILL_QUOTE && ($model->bill_status_id >= BILL_STATUS_APPROVED || $model->quote_bill_id)) {
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

    public function save(array $data, Bill $bill = null)
    {
        $account = $bill ? $bill->account : Auth::user()->account;
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $isNew = !$publicId || intval($publicId) < 0;

        if ($bill) {
            $entityType = $bill->getEntityType();
            $bill->updated_by = Auth::user()->username;

        } elseif ($isNew) {
            $entityType = ENTITY_BILL;
            if (!empty($data['is_recurring']) && filter_var($data['is_recurring'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_RECURRING_BILL;
            } elseif (!empty($data['is_quote']) && filter_var($data['is_quote'], FILTER_VALIDATE_BOOLEAN)) {
                $entityType = ENTITY_BILL_QUOTE;
            }

            $bill = $account->createBill($entityType, $data['client_id']);
            $bill->bill_date = date_create()->format('Y-m-d');
            $bill->due_date = date_create()->format('Y-m-d');
            $bill->custom_taxes1 = $account->custom_bill_taxes1 ?: false;
            $bill->custom_taxes2 = $account->custom_bill_taxes2 ?: false;
            $bill->created_by = Auth::user()->username;
//           set the default due date
            if ($entityType === ENTITY_BILL && empty($data['partial_due_date'])) {
                $vendor = Vendor::scope()->where('id', $data['client_id'])->first();
                $bill->due_date = $account->defaultVendorDueDate($vendor);
            }
            $bill->bill_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
        } else {
            $bill = Bill::scope($publicId)->firstOrFail();
        }
        if (!empty($bill->is_deleted)) {
            return $bill;
        } elseif ($bill->isLocked()) {
            return $bill;
        }

//        if (isset($data['has_tasks']) && filter_var($data['has_tasks'], FILTER_VALIDATE_BOOLEAN)) {
//            $bill->has_tasks = true;
//        }
        if (isset($data['has_expenses']) && filter_var($data['has_expenses'], FILTER_VALIDATE_BOOLEAN)) {
            $bill->has_expenses = true;
        }

        if (isset($data['is_public']) && filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN)) {
            $bill->is_public = true;
            if (!$bill->isSent()) {
                $bill->bill_status_id = BILL_STATUS_SENT;
            }
        }

//     TODO: should be examine this expression
        if ($data['invoice_design_id'] && !$data['invoice_design_id']) {
            $data['invoice_design_id'] = 1;
        }

//      fill bill
        $bill->fill($data);

//      update account default template
        $this->saveAccountDefault($account, $bill, $data);

        if (!empty($data['invoice_number']) && !empty($bill->is_recurring)) {
            $bill->invoice_number = trim($data['invoice_number']);
        }

        if (isset($data['discount'])) {
            $bill->discount = round(Utils::parseFloat($data['discount']), 2);
        }
        if (isset($data['is_amount_discount'])) {
            $bill->is_amount_discount = $data['is_amount_discount'] ? true : false;
        }

        if (!empty($data['bill_date_sql'])) {
            $bill->bill_date = $data['bill_date_sql'];
        } elseif (!empty($data['bill_date'])) {
            $bill->bill_date = Utils::toSqlDate($data['bill_date']);
        }

        /*     if (isset($data['bill_status_id'])) {
                  if ($data['bill_status_id'] == 0) {
                      $data['bill_status_id'] = INVOICE_STATUS_DRAFT;
                  }
                  $bill->bill_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
              } else {
                  $bill->bill_status_id = !empty($data['invoice_status_id']) ? $data['invoice_status_id'] : INVOICE_STATUS_DRAFT;
              }*/

        if (!empty($bill->is_recurring)) {
            if ($isNew && !empty($data['start_date']) && !empty($bill->start_date)
                && $bill->start_date != Utils::toSqlDate($data['start_date'])) {
                $bill->last_sent_date = null;
            }

            $bill->frequency_id = array_get($data, 'frequency_id', FREQUENCY_MONTHLY);
            $bill->start_date = Utils::toSqlDate(array_get($data, 'start_date'));
            $bill->end_date = Utils::toSqlDate(array_get($data, 'end_date'));
            $bill->vendor_enable_auto_bill = !empty($data['client_enable_auto_bill']) && $data['client_enable_auto_bill'] ? true : false;
            $bill->auto_bill = array_get($data, 'auto_bill_id') ?: array_get($data, 'auto_bill', AUTO_BILL_OFF);

            if ($bill->auto_bill < AUTO_BILL_OFF || $bill->auto_bill > AUTO_BILL_ALWAYS) {
                $bill->auto_bill = AUTO_BILL_OFF;
            }

            if (!empty($data['recurring_due_date'])) {
                $bill->due_date = $data['recurring_due_date'];
            } elseif (!empty($data['due_date'])) {
                $bill->due_date = $data['due_date'];
            }
        } else {
            if ($isNew && empty($data['due_date']) && empty($data['due_date_sql'])) {
//           do nothing
            } elseif (!empty($data['due_date']) || !empty($data['due_date_sql'])) {
                $bill->due_date = !empty($data['due_date_sql']) ? $data['due_date_sql'] :
                    Utils::toSqlDate($data['due_date']);
            }
//          bill is not recurring
            $bill->frequency_id = 0;
            $bill->start_date = null;
            $bill->end_date = null;
        }

        if (!empty($data['terms'])) {
            $bill->terms = trim($data['terms']);
        } elseif ($isNew && !empty($bill->is_recurring) && $account->{"{$entityType}_terms"}) {
            $bill->terms = $account->{"{$entityType}_terms"};
        } else {
            $bill->terms = '';
        }

        if (!empty($data['bill_footer'])) {
            $bill->bill_footer = trim($data['bill_footer']);
        } elseif ($isNew && !empty($bill->is_recurring) && !empty($account->bill_footer)) {
            $bill->bill_footer = $account->bill_footer;
        } else {
            $bill->bill_footer = '';
        }

        $bill->public_notes = !empty($data['public_notes']) ? trim($data['public_notes']) : '';

// process date variables if not recurring
        if (!empty($bill->is_recurring)) {
            $bill->terms = Utils::processVariables($bill->terms);
            $bill->bill_footer = Utils::processVariables($bill->bill_footer);
            $bill->public_notes = Utils::processVariables($bill->public_notes);
        }

        if (!empty($data['po_number'])) {
            $bill->po_number = trim($data['po_number']);
        }
//    provide backwards compatibility
        if (!empty($data['tax_name']) && !empty($data['tax_rate'])) {
            $data['tax_name1'] = $data['tax_name'];
            $data['tax_rate1'] = $data['tax_rate'];
        }

//       line item total
        $total = 0;
        $total = $this->getLineItemNetTotal($account, $bill, $data);

//      line item tax
        $itemTax = 0;
        $itemTax = $this->getLineItemNetTax($account, $bill, $data, $total);

//       save bill
        $this->saveBillDetail($account, $bill, $data, $total, $itemTax, $publicId);

        $origLineItems = [];
        if (!empty($publicId)) {
            $origLineItems = !empty($bill->invoice_items) ? $bill->invoice_items()->get()->toArray() : '';
//            remove old bill line items
            $bill->invoice_items()->forceDelete();
        }

//      update if any bill documents
        if (!empty($data['document_ids'])) {
            $document_ids = array_map('intval', $data['document_ids']);
            $this->saveBillDocuments($bill, $document_ids);
            $this->updateBillDocuments($bill, $document_ids);
        }

//      Bill bill line item detail
        $this->saveBillLineItemDetail($account, $bill, $data, $origLineItems, $isNew);

        $this->saveBillInvitations($bill);

//      finally dispatch events
        $this->dispatchEvents($bill);

        return $bill->load('invoice_items');
    }

    private function saveBillInvitations($bill)
    {
        if (empty($bill)) {
            return;
        }

        $vendor = $bill->vendor;

        $vendor->load('contacts');
        $sendBillIds = [];

        if (!$vendor->contacts->count()) {
            return $bill;
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
            $invitation = BillInvitation::scope()
                ->where('contact_id', $contact->id)
                ->where('bill_id', $bill->id)
                ->first();

            if (in_array($contact->id, $sendBillIds) && empty($invitation)) {
                $invitation = BillInvitation::createNew($bill);
                $invitation->bill_id = $bill->id;
                $invitation->contact_id = $contact->id;
                $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                $invitation->created_by = auth()->user()->username;
                $invitation->save();
            } elseif (!in_array($contact->id, $sendBillIds) && !empty($invitation)) {
                $invitation->delete();
            }
        }

        if ($bill->is_public && !$bill->areInvitationsSent()) {
            $bill->markInvitationsSent();
        }

        return $bill;
    }

    private function dispatchEvents($bill)
    {
        if (empty($bill)) {
            return null;
        }
        if ($bill->isType(BILL_TYPE_QUOTE)) {
            if ($bill->wasRecentlyCreated) {
                event(new BillQuoteItemsWereCreatedEvent($bill));
            } else {
                event(new BillQuoteItemsWereUpdatedEvent($bill));
            }
        } else {
            if ($bill->wasRecentlyCreated) {
                event(new BillItemsWereCreatedEvent($bill));
            } else {
                event(new BillItemsWereUpdatedEvent($bill));
            }
        }
    }

    public function cloneBill(Bill $bill, $quoteId = null)
    {
        if (empty($bill)) {
            return null;
        }

        $bill->load('bill_invitations', 'invoice_items');
        $account = $bill->account;

        $clone = Bill::createNew($bill);
        $clone->balance = $bill->amount;

// if the bill prefix is diff than quote prefix, use the same number for the bill (if it's available)
        $invoiceNumber = false;
        if ($account->hasBillPrefix() && $account->share_counter) {
            $invoiceNumber = $bill->invoice_number;
            if ($account->quote_number_prefix && strpos($invoiceNumber, $account->quote_number_prefix) === 0) {
                $invoiceNumber = substr($invoiceNumber, strlen($account->quote_number_prefix));
            }
            $invoiceNumber = $account->invoice_number_prefix . $invoiceNumber;
            $bill = Bill::scope(false, $account->id)
                ->withTrashed()->where('invoice_number', $invoiceNumber)->first();

            if ($bill) {
                $invoiceNumber = false;
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
                     'bill_footer',
                     'public_notes',
                     'invoice_design_id',
                     'tax_name1',
                     'tax_rate1',
                     'tax_name2',
                     'tax_rate2',
                     'amount',
                     'bill_type_id',
                     'custom_value1',
                     'custom_value2',
                     'custom_taxes1',
                     'custom_taxes2',
                     'partial',
                     'custom_text_value1',
                     'custom_text_value2',
                 ] as $field) {
            $clone->$field = $bill->$field;
        }

        if ($quoteId) {
            $clone->bill_type_id = BILL_TYPE_STANDARD;
            $clone->quote_id = $quoteId;
            if ($account->bill_terms) {
                $clone->terms = $account->bill_terms;
            }
            if (!auth()->check()) {
                $clone->is_public = true;
                $clone->bill_status_id = BILL_STATUS_SENT;
            }
        }

        $clone->invoice_number = $invoiceNumber ?: $account->getNextBillNumber($clone);
        $clone->bill_date = date_create()->format('Y-m-d');
        $clone->due_date = $account->defaultDueDate($bill->vendor);
        $clone->bill_status_id = !empty($clone->bill_status_id) ? $clone->bill_status_id : BILL_STATUS_DRAFT;
        $clone->warehouse_id = auth()->user()->branch->warehouse_id;
        $clone->created_by = auth()->user()->username;
        $clone->save();

        if ($quoteId) {
            $bill->bill_status_id = !empty($clone->bill_status_id) ? $clone->bill_status_id : BILL_STATUS_DRAFT;
            $bill->quote_bill_id = $clone->public_id;
            $bill->save();
        }

        foreach ($bill->invoice_items as $item) {
//          bill item instance
            $cloneItem = BillItem::createNew($bill);
            foreach ([
                         'product_id',
                         'product_key',
                         'notes',
                         'cost',
                         'qty',
                         'warehouse_id',
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
                $this->updateItemStore(0, $cloneItem->qty, $itemStore);
            }

            $clone->invoice_items()->save($cloneItem);
        } //end of foreach loop

        foreach ($bill->documents as $document) {
            $cloneDocument = $document->cloneDocument();
            $clone->documents()->save($cloneDocument);
        }

        foreach ($bill->bill_invitations as $invitation) {
            $cloneInvitation = BillInvitation::createNew($bill);
            $cloneInvitation->contact_id = $invitation->contact_id;
            $cloneInvitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $clone->bill_invitations()->save($cloneInvitation);
        }

        $this->dispatchEvents($clone);

        return $clone;
    }

    public function emailBill(Bill $bill)
    {
        if (empty($bill)) {
            return null;
        }

        if (config('queue.default') === 'sync') {
            app('App\Ninja\Mailers\VendorMailer')->sendBill($bill);
        } else {
            dispatch(new SendBillEmail($bill));
        }
    }

    public function markSent(Bill $bill)
    {
        $bill->markSent();
    }

    public function markPaid(Bill $bill)
    {
        if (!$bill->canBePaid()) {
            return null;
        }

        $bill->markSentIfUnsent();

        $data = [
            'vendor_id' => $bill->vendor_id,
            'bill_id' => $bill->id,
            'amount' => $bill->balance,
        ];

        return $this->paymentRepo->save($data);
    }

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

        $bill = $invitation->bill;
        if (empty($bill) || isset($bill->is_deleted)) {
            return false;
        }

        $bill->load('user', 'invoice_items', 'documents', 'bill_design', 'account.country', 'vendor.contacts', 'vendor.country');
        $vendor = $bill->vendor;

        if (empty($vendor) || isset($vendor->is_deleted)) {
            return false;
        }

        return $invitation;
    }

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

    public function getItemStore($account, $product = null)
    {
        if (empty($account) || empty($product)) {
            return false;
        }

        $changeWarehouseId = !empty(auth()->user()->branch->warehouse_id) ? auth()->user()->branch->warehouse_id : 0;

        $itemStore = ItemStore::scope()
            ->where('account_id', $account->id)
            ->where('product_id', $product->id)
            ->where('warehouse_id', $changeWarehouseId)
            ->where('deleted_at', null)
            ->first();

        if ($itemStore) {
            return $itemStore;
        } else {
            $data = [
                'product_id' => $product->id,
                'warehouse_id' => $changeWarehouseId,
            ];

            return $this->getItemStoreInstance($data);
        }

    }


    public function findOpenbills($vendorId)
    {
        if (empty($vendorId)) {
            return false;
        }
        $query = Bill::scope()
            ->billType(BILL_TYPE_STANDARD)
            ->where('vendor_id', $vendorId)
            ->where('is_recurring', false)
            ->where('deleted_at', null)
            ->where('balance', '>', 0);

        return $query->where('bill_status_id', '<', BILL_STATUS_PAID)
            ->select(['public_id', 'invoice_number'])
            ->get();
    }

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

        $bill = Bill::createNew($recurBill);
        $bill->is_public = true;
        $bill->bill_type_id = BILL_TYPE_STANDARD;
        $bill->vendor_id = $recurBill->vendor_id;
        $bill->recurring_bill_id = $recurBill->id;
        $bill->invoice_number = $recurBill->account->getNextBillNumber($bill);
        $bill->amount = $recurBill->amount;
        $bill->balance = $recurBill->amount;
        $bill->bill_date = date_create()->format('Y-m-d');
        $bill->discount = $recurBill->discount;
        $bill->po_number = $recurBill->po_number;
        $bill->public_notes = Utils::processVariables($recurBill->public_notes, $vendor);
        $bill->terms = Utils::processVariables($recurBill->terms ?: $recurBill->account->bill_terms, $vendor);
        $bill->bill_footer = Utils::processVariables($recurBill->bill_footer ?: $recurBill->account->bill_footer, $vendor);
        $bill->tax_name1 = $recurBill->tax_name1;
        $bill->tax_rate1 = $recurBill->tax_rate1;
        $bill->tax_name2 = $recurBill->tax_name2;
        $bill->tax_rate2 = $recurBill->tax_rate2;
        $bill->invoice_design_id = $recurBill->invoice_design_id;
        $bill->custom_value1 = $recurBill->custom_value1 ?: 0;
        $bill->custom_value2 = $recurBill->custom_value2 ?: 0;
        $bill->custom_taxes1 = $recurBill->custom_taxes1 ?: 0;
        $bill->custom_taxes2 = $recurBill->custom_taxes2 ?: 0;
        $bill->custom_text_value1 = Utils::processVariables($recurBill->custom_text_value1, $vendor);
        $bill->custom_text_value2 = Utils::processVariables($recurBill->custom_text_value2, $vendor);
        $bill->is_amount_discount = $recurBill->is_amount_discount;
        $bill->due_date = $recurBill->getDueDate();
        $bill->save();

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

            $bill->invoice_items()->save($item);
        }

        foreach ($recurBill->documents as $recurDocument) {
            $document = $recurDocument->cloneDocument();
            $bill->documents()->save($document);
        }

        foreach ($recurBill->bill_invitations as $recurInvitation) {
            $invitation = BillInvitation::createNew($recurInvitation);
            $invitation->contact_id = $recurInvitation->contact_id;
            $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $bill->bill_invitations()->save($invitation);
        }

        $recurBill->last_sent_date = date('Y-m-d');
        $recurBill->save();

        if ($recurBill->getAutoBillEnabled() && !$recurBill->account->auto_bill_on_due_date) {
// autoBillBill will check for ACH, so we're not checking here
            if ($this->paymentService->autoBill($bill)) {
// update the bill reference to match its actual state
// this is to ensure a 'payment received' email is sent
                $bill->bill_status_id = BILL_STATUS_PAID;
            }
        }

        $this->dispatchEvents($bill);

        return $bill;
    }

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
                    $dates[] = "bill_date = '$date'";
                }
            }
        }

        if (!count($dates)) {
            return collect();
        }

        $sql = implode(' OR ', $dates);
        $bills = Bill::billType(BILL_TYPE_STANDARD)
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

        return $bills;
    }

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

        $bills = Bill::billType(BILL_TYPE_STANDARD)
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
            $field = $account->{"field_reminder{$i}"} == REMINDER_FIELD_DUE_DATE ? 'due_date' : 'bill_date';
            $date = date_create();
            if ($account->{"direction_reminder{$i}"} == REMINDER_DIRECTION_AFTER) {
                $date->sub(date_interval_create_from_date_string($account->{"num_days_reminder{$i}"} . ' days'));
            }
            $bills->where($field, '<', $date);
        }

        return $bills->get();
    }

    public function clearGatewayFee($bill)
    {
        if (empty($bill)) {
            return false;
        }

        $account = $bill->account;

        if (!$bill->relationLoaded('invoice_items')) {
            $bill->load('invoice_items');
        }

        $data = $bill->toArray();
        foreach ($data['invoice_items'] as $key => $item) {
            if ($item['bill_item_type_id'] == BILL_ITEM_TYPE_PENDING_GATEWAY_FEE) {
                unset($data['invoice_items'][$key]);
                $this->save($data, $bill);
                break;
            }
        }

        return true;
    }

    public function setLateFee($bill, $amount, $percent)
    {
        if (empty($bill)) {
            return false;
        }

        if ($amount <= 0 && $percent <= 0) {
            return false;
        }

        $account = $bill->account;

        $data = $bill->toArray();
        $fee = $amount;

        if ($bill->getRequestedAmount() > 0) {
            $fee += round($bill->getRequestedAmount() * $percent / 100, 2);
        }

        $item = [];
        $item['product_key'] = trans('texts.fee');
        $item['notes'] = trans('texts.late_fee_added', ['date' => $account->formatDate('now')]);
        $item['qty'] = 1;
        $item['cost'] = $fee;
        $item['bill_item_type_id'] = BILL_ITEM_TYPE_LATE_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $bill);

        return true;
    }

    public function setGatewayFee($bill, $gatewayTypeId)
    {
        if (empty($bill)) {
            return false;
        }

        $account = $bill->account;

        if (!isset($account->gateway_fee_enabled)) {
            return false;
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);
        $this->clearGatewayFee($bill);

        if (empty($settings)) {
            return false;
        }

        $data = $bill->toArray();
        $fee = $bill->calcGatewayFee($gatewayTypeId);
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
        $item['bill_item_type_id'] = BILL_ITEM_TYPE_PENDING_GATEWAY_FEE;
        $data['invoice_items'][] = $item;

        $this->save($data, $bill);

        return true;
    }

    public function findPhonetically($invoiceNumber)
    {
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $billId = 0;

        $bills = Bill::scope()->get(['id', 'invoice_number', 'public_id']);

        foreach ($bills as $bill) {
            $map[$bill->id] = $bill;
            $similar = similar_text($invoiceNumber, $bill->invoice_number, $percent);
            if ($percent > $max) {
                $billId = $bill->id;
                $max = $percent;
            }
        }

        return ($billId && !empty($map[$billId])) ? $map[$billId] : null;
    }

    private function getExpense(Bill $bill, array $item)
    {
        if (empty($item['expense_public_id'])) {
            return false;
        }

        $expense = Expense::scope($item['expense_public_id'])
            ->where('bill_id', null)->firstOrFail();
        if (Auth::user()->can('edit', $expense)) {
            $expense->bill_id = $bill->id;
            $expense->vendor_id = $bill->vendor_id;
            if ($expense->save()) {
                return true;
            }
        }

        return false;
    }

    private function getTask(Bill $bill, array $item)
    {
        if (empty($item['task_public_id'])) {
            return false;
        }

        $task = Task::scope(trim($item['task_public_id']))
            ->whereNull('bill_id')->firstOrFail();
        if (Auth::user()->can('edit', $task)) {
            $task->bill_id = $bill->id;
            $task->vendor_id = $bill->vendor_id;
            if ($task->save()) {
                return true;
            }
        }

        return false;
    }


    private function saveBillDocuments(Bill $bill, array $document_ids)
    {
        if (empty($bill) || empty($document_ids)) {
            return false;
        }

        foreach ($document_ids as $document_id) {
            $document = Document::scope($document_id)->first();
            if ($document && Auth::user()->can('edit', $document)) {
                if ($document->bill_id && $document->bill_id != $bill->id) {
//                From a clone
                    $document = $document->cloneDocument();
                    $document_ids[] = $document->public_id; // Don't remove this document
                }
                $document->bill_id = $bill->id;
                $document->expense_id = null;
                $document->save();
            }
        }

        return true;
    }

    private function updateBillDocuments(Bill $bill, array $document_ids)
    {
        if (empty($bill) || empty($document_ids)) {
            return false;
        }
        if (!$bill->wasRecentlyCreated) {
            foreach ($bill->documents as $document) {
                if (!in_array($document->public_id, $document_ids)) {
                    if (Auth::user()->can('delete', $document)) {
// Not checking permissions; deleting a document is just editing the bill
                        if ($document->bill_id === $bill->id) {
// Make sure the document isn't on a clone
                            $document->delete();
                        }
                    }
                }
            }
        }

        return true;
    }

    public function adjustBillItems($billItems)
    {
        if (empty($billItems)) {
            return;
        }

        foreach ($billItems as $billItem) {
            $qty = $billItem['qty'];
            $qoh = ItemStore::scope()->where('product_id', $billItem['product_id'])
                ->where('warehouse_id', $billItem['warehouse_id'])
                ->first();
            if ($qoh) {
                $stockQty = $qoh->qty;
                $stockQty -= $qty;
                $qoh->update(['qty' => $stockQty]);
            }
        }

    }

    public function restoreBillItems($billItems)
    {
        if (empty($billItems)) {
            return;
        }

        foreach ($billItems as $billItem) {
            $qty = $billItem['qty'];
            $qoh = ItemStore::scope()->where('product_id', $billItem['product_id'])
                ->where('warehouse_id', $billItem['warehouse_id'])
                ->first();
            if ($qoh) {
                $stockQty = $qoh->qty;
                $stockQty += $qty;
                $qoh->update(['qty' => $stockQty]);
            }
        }

    }

    private function stockIn($itemStore, Bill $bill, $origLineItems, array $newLineItem, $isNew)
    {
        $qoh = !empty($itemStore) ? Utils::parseFloat($itemStore->qty) : 0;
        $receivedQty = Utils::parseFloat(trim($newLineItem['qty']));

//        $Bill = Bill::whereName($productKey);
//        $orderQty = Utils::parseFloat(0);
        if ($isNew) {
            $this->updateItemStore(0, $receivedQty, $itemStore);
        } else {
            $found = 0;
            $origLineItems = (array)$origLineItems;
            if (count($origLineItems)) {
                foreach ($origLineItems as $origLineItem) {
                    if ($newLineItem['product_key'] === $origLineItem['product_key']) {
                        if (($newLineItem['qty'] != $origLineItem['qty'])) {
                            $origQty = $origLineItem['qty'];
                            $this->updateItemStore($origQty, $receivedQty, $itemStore);
                            $found += 1;
                            break;
                        } else {
                            $found += 1;
                        }
                        break;
                    }
                }
//              new item
                if ($found == 0) {
                    $this->updateItemStore(0, $receivedQty, $itemStore);
                }
            } else {
//              if it's one item
                $this->updateItemStore(0, $receivedQty, $itemStore);
            }
        }

        return true;
    }

    private function saveBillLineItemAdjustment(Bill $bill, $product, array $item)
    {
        $billQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $receivedQty = !empty($item['qty']) ? Utils::parseFloat(trim($item['qty'])) : 1;
        $itemCost = !empty($item['cost']) ? Utils::parseFloat(trim($item['cost'])) : (isset($product->cost) ? $product->cost : 0);
        $billItem = BillItem::createNew($bill);
        $billItem->fill($item);
        $billItem->product_id = !empty($product->id) ? $product->id : null;
        $billItem->warehouse_id = !empty($bill->warehouse_id) ? $bill->warehouse_id : auth()->user()->branch->warehouse_id;
        $billItem->product_key = !empty($item['product_key']) ? trim($item['product_key']) : null;
        $billItem->notes = !empty($item['notes']) ? trim($item['notes']) : null;
        $billItem->cost = $itemCost;
        $billItem->qty = $billQty;
        $billItem->received_qty = $receivedQty;
        $billItem->discount = $bill->discount;
        $billItem->created_by = auth()->user()->username;

        if (!empty($item['custom_value1'])) {
            $billItem->custom_value1 = $item['custom_value1'];
        }
        if (!empty($item['custom_value2'])) {
            $billItem->custom_value2 = $item['custom_value2'];
        }
// provide backwards compatibility
        if (!empty($item['tax_name']) && !empty($item['tax_rate'])) {
            $item['tax_name1'] = $item['tax_name'];
            $item['tax_rate1'] = $item['tax_rate'];
        }

// provide backwards compatibility
        if (!empty($item['invoice_item_type_id']) && in_array($billItem->notes, [trans('texts.online_payment_surcharge'), trans('texts.online_payment_discount')])) {
            $billItem->bill_item_type_id = $bill->balance > 0 ? BILL_ITEM_TYPE_PENDING_GATEWAY_FEE : BILL_ITEM_TYPE_PAID_GATEWAY_FEE;
        }

        $billItem->fill($item);

        $bill->invoice_items()->save($billItem);

        return true;
    }

    private function saveAccountDefault($account, Bill $bill, array $data)
    {
        if (empty($bill)) {
            return false;
        }

        if ((!empty($data['set_default_terms']) && $data['set_default_terms'])
            || (!empty($data['set_default_footer']) && $data['set_default_footer'])) {
            if (!empty($data['set_default_terms']) && $data['set_default_terms']) {
                $account->{"{$bill->getEntityType()}_terms"} = trim($data['bill_terms']);
            }
            if (!empty($data['set_default_footer']) && $data['set_default_footer']) {
                $account->bill_footer = trim($data['bill_footer']);
            }

            $account->save();
        }

        return true;
    }


    private function getLineItemNetTotal($account, Bill $bill, array $data)
    {
        $total = 0;
        $data = (array)$data;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                if (empty($item['cost']) || empty($item['product_key'])) {
                    continue;
                }
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $billItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : $product->cost;
                    $billItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                    $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                    $total = $this->getLineItemTotal($bill, $billItemCost, $billItemQty, $discount, $total);
                } else {
                    $total = $this->getLineItemTotal($bill, trim($item['cost']), trim($item['qty']), trim($item['discount']), $total);
                }
            }
        }

        return $total;
    }

    private function getLineItemNetTax($account, Bill $bill, array $data, $total)
    {
        $itemTax = 0;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                $product = $this->getProductDetail($account, $item['product_key']);
                if (!empty($product)) {
                    $billItemCost = !empty($item['cost']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['cost']))) : (isset($product->cost) ? $product->cost : 0);
                    $billItemQty = !empty($item['qty']) ? Utils::roundSignificant(Utils::parseFloat(trim($item['qty']))) : 1;
                    $discount = !empty($item['discount']) ? trim($item['discount']) : 0;
                    $itemTax = $this->getLineItemTaxTotal($bill, $total, $billItemCost, $billItemQty, $item, $itemTax);
                } else {
                    $itemTax = $this->getLineItemTaxTotal($bill, $total, trim($item['cost']), trim($item['qty']), $item, $itemTax);
                }
            }
        }

        return $itemTax;
    }

    private function updateItemStore($origQty, $receivedQty, $itemStore)
    {
        $qoh = Utils::parseFloat($itemStore->qty);
        $origQty = Utils::parseFloat($origQty);
        $receivedQty = Utils::parseFloat($receivedQty);
        $qoh = Utils::parseFloat($itemStore->qty);
        if ($origQty > $receivedQty) {
            $diffQty = $origQty - $receivedQty;
            $itemStore->qty = $qoh - $diffQty;
            $itemStore->save();
        } else if ($receivedQty > $origQty) {
            $diffQty = $receivedQty - $origQty;
            $itemStore->qty = $qoh + $diffQty;
            $itemStore->save();
        }

        return true;
    }

    private function getLineItemTotal(Bill $bill, $billItemCost, $billItemQty, $discount, $total)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $discount = !empty($discount) ? Utils::parseFloat($discount) : 0;
        $lineTotal = floatval($billItemCost) * floatval($billItemQty);
        if ($discount) {
            if (!empty($bill->is_amount_discount)) {
                $lineTotal -= Utils::parseFloat($discount);
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }

        $total += round($lineTotal, 2);

        return $total;
    }

    private function getLineItemTaxTotal(Bill $bill, $total, $billItemCost, $billItemQty, array $item, $itemTax)
    {
        $total = Utils::parseFloat($total);
        $itemTax = Utils::parseFloat($itemTax);
        $discount = !empty($item['discount']) ? round(Utils::parseFloat($item['discount']), 2) : 0;
        $lineTotal = floatval($billItemCost) * floatval($billItemQty);
        if ($discount) {
            if (!empty($bill->is_amount_discount)) {
                $lineTotal -= $discount;
            } else {
                $lineTotal -= round(($lineTotal * $discount / 100), 4);
            }
        }
//          if any bill discount
        $billDiscount = !empty($bill->discount) ? Utils::parseFloat($bill->discount) : 0;

        if ($billDiscount) {
            if (!empty($bill->is_amount_discount)) {
                if (!empty($total) && $total > 0) {
                    $lineTotal -= round($lineTotal / $total * $billDiscount, 4);
                }
            } else {
                $lineTotal -= round(($lineTotal * $billDiscount / 100), 4);
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

    private function saveBillLineItemDetail($account, Bill $bill, array $data, $origLineItems, $isNew)
    {
        $product = null;
        $itemStore = null;
        if (is_array($data)) {
            foreach ($data['invoice_items'] as $item) {
                $item = (array)$item;
                if (empty($item['product_key']) || empty($item['cost'])) {
                    continue;
                }
                $product = $this->getProductDetail($account, $item['product_key']);
//                if (!empty($data['has_tasks'])) {
//                    $this->getTask($bill, $item);
//                }
//                if (!empty($data['has_expenses'])) {
//                    $this->getExpense($bill, $item);
//                }
//              item if not service and labor
                if (!empty($product) && $product->item_type_id !== SERVICE_OR_LABOUR) {
                    $itemStore = $this->getItemStore($account, $product);
                    if (!empty($itemStore)) {
                        // i couldn't find efficient evaluation for false expression, $data['has_tasks']== false and empty value
//                    $is_quote = empty($data['is_quote']) ? $data['is_quote'] : null;
                        //  has taks empty value cannot be evaluated
//                    $has_tasks = $data['has_tasks'] ? $data['has_tasks'] : null;
//                  what if invoices, quotes, expenses and tasks
                        if (empty($data['is_quote'])) {
                            $this->stockIn($itemStore, $bill, $origLineItems, $item, $isNew);
                        }
                    }
                    $this->saveBillLineItemAdjustment($bill, $product, $item);
                } else {
                    $this->saveBillLineItemAdjustment($bill, $product, $item);
                }

            } // end of foreach loop

            return true;
        }
    }

    /**
     * @param array $data
     * @param Bill $bill
     * @param float $total
     * @param $account
     * @param $itemTax
     * @param bool $publicId
     * @return mixed|null
     */
    private function saveBillDetail($account, Bill $bill, array $data, $total, $itemTax, $publicId)
    {
        $total = !empty($total) ? Utils::parseFloat($total) : 0;
        $billDiscount = !empty($bill->discount) ? Utils::parseFloat($bill->discount) : 0;
//      if any bill discount
        if ($billDiscount) {
            if (!empty($bill->is_amount_discount)) {
                $total -= $billDiscount;
            } else {
                $discount = round($total * ($billDiscount / 100), 2);
                $total -= $discount;
            }
        }

        if (!empty($data['custom_value1'])) {
            $bill->custom_value1 = round($data['custom_value1'], 2);
        }
        if (!empty($data['custom_value2'])) {
            $bill->custom_value2 = round($data['custom_value2'], 2);
        }

        if (!empty($data['custom_text_value1'])) {
            $bill->custom_text_value1 = trim($data['custom_text_value1']);
        }
        if (!empty($data['custom_text_value2'])) {
            $bill->custom_text_value2 = trim($data['custom_text_value2']);
        }

// custom fields charged taxes
        if ($bill->custom_value1 && $bill->custom_taxes1) {
            $total += $bill->custom_value1;
        }
        if ($bill->custom_value2 && $bill->custom_taxes2) {
            $total += $bill->custom_value2;
        }

        if (!empty($account->inclusive_taxes)) {
            $taxAmount1 = round($total * ($bill->tax_rate1 ? $bill->tax_rate1 : 0) / 100, 2);
            $taxAmount2 = round($total * ($bill->tax_rate2 ? $bill->tax_rate2 : 0) / 100, 2);

            $total = round($total + $taxAmount1 + $taxAmount2, 2);
            $total += $itemTax;
        }

// custom fields not charged taxes
        if ($bill->custom_value1 && !$bill->custom_taxes1) {
            $total += $bill->custom_value1;
        }
        if ($bill->custom_value2 && !$bill->custom_taxes2) {
            $total += $bill->custom_value2;
        }

        if (!empty($publicId)) {
            $bill->balance = round($total - ($bill->amount - $bill->balance), 2);
        } else {
            $bill->balance = $total;
        }

        if (!empty($data['partial'])) {
            $bill->partial = max(0, min(round(Utils::parseFloat($data['partial']), 2), $bill->balance));
        }

        if (!empty($bill->partial)) {
            if (!empty($data['partial_due_date'])) {
                $bill->partial_due_date = Utils::toSqlDate($data['partial_due_date']);
            }
        } else {
            $bill->partial_due_date = null;
        }

        $bill->amount = $total;
        $bill->warehouse_id = !empty($bill->warehouse_id) ? $bill->warehouse_id : auth()->user()->branch->warehouse_id;
        $bill->is_received = true;

        $bill = $bill->save();

        return $bill;
    }

}
