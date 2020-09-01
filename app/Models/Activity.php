<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ActivityListener.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property int|null $client_id
 * @property int|null $contact_id
 * @property int|null $vendor_id
 * @property int|null $vendor_contact_id
 * @property int|null $payment_id
 * @property int|null $bill_payment_id
 * @property int|null $credit_id
 * @property int|null $VENDOR_CREDIT_id
 * @property int|null $invitation_id
 * @property int|null $bill_invitation_id
 * @property int|null $task_id
 * @property int|null $expense_id
 * @property string|null $json_backup
 * @property int $activity_type_id
 * @property float|null $adjustment
 * @property float|null $balance
 * @property int|null $token_id
 * @property string|null $ip
 * @property int $is_system
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Bill|null $bill
 * @property-read BillCredit|null $VENDOR_CREDIT
 * @property-read BillPayment|null $bill_payment
 * @property-read Client|null $client
 * @property-read Contact|null $contact
 * @property-read Credit|null $credit
 * @property-read Expense|null $expense
 * @property-read Invoice|null $invoice
 * @property-read Payment|null $payment
 * @property-read Task|null $task
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @property-read VendorContact|null $vendor_contact
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Activity whereAccountId($value)
 * @method static Builder|Activity whereActivityTypeId($value)
 * @method static Builder|Activity whereAdjustment($value)
 * @method static Builder|Activity whereBalance($value)
 * @method static Builder|Activity whereBillCreditId($value)
 * @method static Builder|Activity whereBillId($value)
 * @method static Builder|Activity whereBillInvitationId($value)
 * @method static Builder|Activity whereBillPaymentId($value)
 * @method static Builder|Activity whereClientId($value)
 * @method static Builder|Activity whereContactId($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereCreatedBy($value)
 * @method static Builder|Activity whereCreditId($value)
 * @method static Builder|Activity whereDeletedAt($value)
 * @method static Builder|Activity whereDeletedBy($value)
 * @method static Builder|Activity whereExpenseId($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereInvitationId($value)
 * @method static Builder|Activity whereInvoiceId($value)
 * @method static Builder|Activity whereIp($value)
 * @method static Builder|Activity whereIsSystem($value)
 * @method static Builder|Activity whereJsonBackup($value)
 * @method static Builder|Activity whereNotes($value)
 * @method static Builder|Activity wherePaymentId($value)
 * @method static Builder|Activity wherePublicId($value)
 * @method static Builder|Activity whereTaskId($value)
 * @method static Builder|Activity whereTokenId($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereUpdatedBy($value)
 * @method static Builder|Activity whereUserId($value)
 * @method static Builder|Activity whereVendorContactId($value)
 * @method static Builder|Activity whereVendorId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
class Activity extends EntityModel
{
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ActivityPresenter';

    protected $table = 'activities';

    public $timestamps = true;


    // public function scopeScope($query)
    // {
    //     return $query->where('account_id',Auth::user()->account_id);
    // }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo('App\Models\Contact')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    public function vendor_contact()
    {
        return $this->belongsTo('App\Models\VendorContact')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function bill()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
    }

    public function credit()
    {
        return $this->belongsTo('App\Models\Credit')->withTrashed();
    }

    public function vendor_credit()
    {
        return $this->belongsTo('App\Models\VendorCredit')->withTrashed();
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment')->withTrashed();
    }

    public function bill_payment()
    {
        return $this->belongsTo('App\Models\BillPayment')->withTrashed();
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Task')->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo('App\Models\Expense')->withTrashed();
    }

    public function key()
    {
        return sprintf('%s-%s-%s', $this->activity_type_id, $this->client_id, $this->created_at->timestamp);
    }

    public function getMessage()
    {
        $activityTypeId = $this->activity_type_id;
        $account = $this->account;
        $client = $this->client;
        $contactId = $this->contact_id;
        $vendor = $this->vendor;
        $vendorContactId = $this->vendor_contact_id;
        $user = $this->user;
        $invoice = $this->invoice;
        $bill = $this->bill;
        $contactVendorId = $this->vendor_contact_id;
        $payment = $this->payment;
        $billPayment = $this->bill_payment;
        $credit = $this->credit;
        $BillCredit = $this->VENDOR_CREDIT;
        $expense = $this->expense;
        $isSystem = $this->is_system;
        $task = $this->task;

        $data = [
            'client' => $client ? link_to($client->getRoute(), $client->getDisplayName()) : null,
            'vendor' => $vendor ? link_to($vendor->getRoute(), $vendor->getDisplayName()) : null,
            'user' => $isSystem ? '<i>' . trans('texts.system') . '</i>' : e($user->getDisplayName()),
            'invoice' => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
            'bill' => $bill ? link_to($bill->getRoute(), $bill->getDisplayName()) : null,
            'quote' => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
            'bill_quote' => $bill ? link_to($bill->getRoute(), $bill->getDisplayName()) : null,
            'contact' => $contactId ? link_to($client->getRoute(), $client->getDisplayName()) : e($user->getDisplayName()),
            'vendor_contact' => $vendorContactId ? link_to($vendor->getRoute(), $vendor->getDisplayName()) : e($user->getDisplayName()),
            'payment' => $payment ? e($payment->transaction_reference) : null,
            'bill_payment' => $billPayment ? e($billPayment->transaction_reference) : null,
            'payment_amount' => $payment ? $account->formatMoney($payment->amount, $payment) : null,
            'adjustment' => $this->adjustment ? $account->formatMoney($this->adjustment, $this) : null,
            'credit' => $credit ? $account->formatMoney($credit->amount, $client) : null,
            'VENDOR_CREDIT' => $BillCredit ? $account->formatMoney($credit->amount, $client) : null,
            'task' => $task ? link_to($task->getRoute(), substr($task->description, 0, 30) . '...') : null,
            'expense' => $expense ? link_to($expense->getRoute(), substr($expense->public_notes, 0, 30) . '...') : null,
        ];

        return trans("texts.activity_{$activityTypeId}", $data);
    }

    public function relatedEntityType()
    {
        switch ($this->activity_type_id) {
            case ACTIVITY_TYPE_CREATE_CLIENT:
            case ACTIVITY_TYPE_ARCHIVE_CLIENT:
            case ACTIVITY_TYPE_DELETE_CLIENT:
            case ACTIVITY_TYPE_RESTORE_CLIENT:
            case ACTIVITY_TYPE_CREATE_CREDIT:
            case ACTIVITY_TYPE_ARCHIVE_CREDIT:
            case ACTIVITY_TYPE_DELETE_CREDIT:
            case ACTIVITY_TYPE_RESTORE_CREDIT:
                return ENTITY_CLIENT;
                break;
            case ACTIVITY_TYPE_CREATE_VENDOR:
            case ACTIVITY_TYPE_ARCHIVE_VENDOR:
            case ACTIVITY_TYPE_DELETE_VENDOR:
            case ACTIVITY_TYPE_RESTORE_VENDOR:
            case ACTIVITY_TYPE_CREATE_VENDOR_CREDIT:
            case ACTIVITY_TYPE_ARCHIVE_VENDOR_CREDIT:
            case ACTIVITY_TYPE_DELETE_VENDOR_CREDIT:
            case ACTIVITY_TYPE_RESTORE_VENDOR_CREDIT:
                return ENTITY_VENDOR;
                break;
            case ACTIVITY_TYPE_CREATE_INVOICE:
            case ACTIVITY_TYPE_UPDATE_INVOICE:
            case ACTIVITY_TYPE_EMAIL_INVOICE:
            case ACTIVITY_TYPE_VIEW_INVOICE:
            case ACTIVITY_TYPE_ARCHIVE_INVOICE:
            case ACTIVITY_TYPE_DELETE_INVOICE:
            case ACTIVITY_TYPE_RESTORE_INVOICE:
                return ENTITY_INVOICE;
                break;
            case ACTIVITY_TYPE_CREATE_BILL:
            case ACTIVITY_TYPE_UPDATE_BILL:
            case ACTIVITY_TYPE_EMAIL_BILL:
            case ACTIVITY_TYPE_VIEW_BILL:
            case ACTIVITY_TYPE_ARCHIVE_BILL:
            case ACTIVITY_TYPE_DELETE_BILL:
            case ACTIVITY_TYPE_RESTORE_BILL:
                return ENTITY_BILL;
                break;
            case ACTIVITY_TYPE_CREATE_PAYMENT:
            case ACTIVITY_TYPE_ARCHIVE_PAYMENT:
            case ACTIVITY_TYPE_DELETE_PAYMENT:
            case ACTIVITY_TYPE_RESTORE_PAYMENT:
            case ACTIVITY_TYPE_VOIDED_PAYMENT:
            case ACTIVITY_TYPE_REFUNDED_PAYMENT:
            case ACTIVITY_TYPE_FAILED_PAYMENT:
                return ENTITY_PAYMENT;
                break;
            case ACTIVITY_TYPE_CREATE_BILL_PAYMENT:
            case ACTIVITY_TYPE_ARCHIVE_BILL_PAYMENT:
            case ACTIVITY_TYPE_DELETE_BILL_PAYMENT:
            case ACTIVITY_TYPE_RESTORE_BILL_PAYMENT:
            case ACTIVITY_TYPE_VOIDED_BILL_PAYMENT:
            case ACTIVITY_TYPE_REFUNDED_BILL_PAYMENT:
            case ACTIVITY_TYPE_FAILED_BILL_PAYMENT:
                return ENTITY_BILL_PAYMENT;
                break;
            case ACTIVITY_TYPE_CREATE_QUOTE:
            case ACTIVITY_TYPE_UPDATE_QUOTE:
            case ACTIVITY_TYPE_EMAIL_QUOTE:
            case ACTIVITY_TYPE_VIEW_QUOTE:
            case ACTIVITY_TYPE_ARCHIVE_QUOTE:
            case ACTIVITY_TYPE_DELETE_QUOTE:
            case ACTIVITY_TYPE_RESTORE_QUOTE:
            case ACTIVITY_TYPE_APPROVE_QUOTE:
                return ENTITY_QUOTE;
                break;
            case ACTIVITY_TYPE_CREATE_bill_quote:
            case ACTIVITY_TYPE_UPDATE_bill_quote:
            case ACTIVITY_TYPE_EMAIL_BILL_QUOTE:
            case ACTIVITY_TYPE_VIEW_bill_quote:
            case ACTIVITY_TYPE_ARCHIVE_bill_quote:
            case ACTIVITY_TYPE_DELETE_bill_quote:
            case ACTIVITY_TYPE_RESTORE_bill_quote:
            case ACTIVITY_TYPE_APPROVE_bill_quote:
                return ENTITY_BILL_QUOTE;
                break;
//            case ACTIVITY_TYPE_CREATE_VENDOR:
//            case ACTIVITY_TYPE_ARCHIVE_VENDOR:
//            case ACTIVITY_TYPE_DELETE_VENDOR:
//            case ACTIVITY_TYPE_RESTORE_VENDOR:
            case ACTIVITY_TYPE_CREATE_EXPENSE:
            case ACTIVITY_TYPE_ARCHIVE_EXPENSE:
            case ACTIVITY_TYPE_DELETE_EXPENSE:
            case ACTIVITY_TYPE_RESTORE_EXPENSE:
            case ACTIVITY_TYPE_UPDATE_EXPENSE:
                return ENTITY_EXPENSE;
                break;

            case ACTIVITY_TYPE_CREATE_TASK:
            case ACTIVITY_TYPE_UPDATE_TASK:
            case ACTIVITY_TYPE_ARCHIVE_TASK:
            case ACTIVITY_TYPE_DELETE_TASK:
            case ACTIVITY_TYPE_RESTORE_TASK:
                return ENTITY_TASK;
                break;
        }
    }

}
