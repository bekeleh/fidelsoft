<?php

namespace App\Models;

use App\Events\Sale\PaymentCompletedEvent;
use App\Events\Sale\PaymentFailedEvent;
use App\Events\Sale\PaymentWasCreatedEvent;
use App\Events\Sale\PaymentWasRefundedEvent;
use App\Events\Sale\PaymentWasVoidedEvent;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Laracasts\Presenter\PresentableTrait;


/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $invoice_id
 * @property int|null $client_id
 * @property int|null $contact_id
 * @property int|null $account_gateway_id
 * @property int|null $payment_type_id
 * @property int|null $user_id
 * @property int|null $payment_status_id
 * @property int|null $payment_method_id
 * @property int|null $exchange_currency_id
 * @property int|null $invitation_id
 * @property string|null $payer_id
 * @property float $amount
 * @property float $refunded
 * @property string|null $payment_date
 * @property string|null $transaction_reference
 * @property int|null $routing_number
 * @property int|null $last4
 * @property string|null $expiration
 * @property string|null $gateway_error
 * @property string|null $email
 * @property string|null $bank_name
 * @property string|null $ip
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property string|null $credit_ids
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property float $exchange_rate
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Account|null $account
 * @property-read AccountGateway|null $account_gateway
 * @property-read Client|null $client
 * @property-read Contact|null $contact
 * @property-read mixed $bank_data
 * @property-read Invitation|null $invitation
 * @property-read Invoice|null $invoice
 * @property-read PaymentMethod|null $payment_method
 * @property-read PaymentStatus|null $payment_status
 * @property-read PaymentType|null $payment_type
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Payment dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment excludeFailed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static Builder|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreditIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExchangeCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvitationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereLast4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRoutingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Payment withTrashed()
 * @method static Builder|Payment withoutTrashed()
 * @mixin Eloquent
 */
class Payment extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'transaction_reference',
        'public_notes',
        'private_notes',
        'exchange_rate',
        'exchange_currency_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static $statusClasses = [
        PAYMENT_STATUS_PENDING => 'info',
        PAYMENT_STATUS_COMPLETED => 'success',
        PAYMENT_STATUS_PARTIALLY_PAID => 'primary',
        PAYMENT_STATUS_FAILED => 'danger',
        PAYMENT_STATUS_PARTIALLY_REFUNDED => 'primary',
        PAYMENT_STATUS_VOIDED => 'default',
        PAYMENT_STATUS_REFUNDED => 'default',
        PAYMENT_STATUS_ADVANCE_PAID => 'success',
    ];


    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $presenter = 'App\Ninja\Presenters\PaymentPresenter';

    public function getEntityType()
    {
        return ENTITY_PAYMENT;
    }

    public function getRoute()
    {
        return "/payments/{$this->public_id}/edit";
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }


    public function invitation()
    {
        return $this->belongsTo('App\Models\Invitation');
    }


    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }


    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }


    public function contact()
    {
        return $this->belongsTo('App\Models\Contact')->withTrashed();
    }


    public function account_gateway()
    {
        return $this->belongsTo('App\Models\Common\AccountGateway')->withTrashed();
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\PaymentType');
    }


    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod');
    }

    public function payment_status()
    {
        return $this->belongsTo('App\Models\PaymentStatus');
    }

    public function scopeExcludeFailed($query)
    {
        $query->whereNotIn('payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        return $query;
    }


    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function getName()
    {
        return trim("payment {$this->transaction_reference}");
    }

    public function isPending()
    {
        return $this->payment_status_id == PAYMENT_STATUS_PENDING;
    }

    public function isFailed()
    {
        return $this->payment_status_id == PAYMENT_STATUS_FAILED;
    }

    public function isCompleted()
    {
        return $this->payment_status_id == PAYMENT_STATUS_COMPLETED;
    }


    public function isPartiallyRefunded()
    {
        return $this->payment_status_id == PAYMENT_STATUS_PARTIALLY_REFUNDED;
    }

    public function isPartiallyPaid()
    {
        return $this->payment_status_id == PAYMENT_STATUS_PARTIALLY_PAID;
    }


    public function isRefunded()
    {
        return $this->payment_status_id == PAYMENT_STATUS_REFUNDED;
    }

    public function isVoided()
    {
        return $this->payment_status_id == PAYMENT_STATUS_VOIDED;
    }

    public function isAdvancePaid()
    {
        return $this->payment_status_id == PAYMENT_STATUS_ADVANCE_PAID;
    }

    public function isFailedOrVoided()
    {
        return $this->isFailed() || $this->isVoided();
    }

    public function recordRefund($amount = null)
    {
        if ($this->isRefunded() || $this->isVoided()) {
            return false;
        }

        if (!$amount) {
            $amount = $this->amount;
        }

        $new_refund = min($this->amount, $this->refunded + $amount);
        $refund_change = $new_refund - $this->refunded;

        if ($refund_change) {
            $this->refunded = $new_refund;
            $this->payment_status_id = $this->refunded == $this->amount ? PAYMENT_STATUS_REFUNDED : PAYMENT_STATUS_PARTIALLY_REFUNDED;
            $this->save();

            Event::fire(new PaymentWasRefundedEvent($this, $refund_change));
        }

        return true;
    }

    public function markVoided()
    {
        if ($this->isVoided() || $this->isPartiallyRefunded() || $this->isRefunded()) {
            return false;
        }

        Event::fire(new PaymentWasVoidedEvent($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
    }

    public function markComplete()
    {
        $this->payment_status_id = PAYMENT_STATUS_COMPLETED;
        $this->save();
        Event::fire(new PaymentCompletedEvent($this));
    }

    public function markFailed($failureMessage = '')
    {
        $this->payment_status_id = PAYMENT_STATUS_FAILED;
        $this->gateway_error = $failureMessage;
        $this->save();
        Event::fire(new PaymentFailedEvent($this));
    }

    public function getCompletedAmount()
    {
        return $this->amount - $this->refunded;
    }

    public function canBeRefunded()
    {
        return $this->getCompletedAmount() > 0 && ($this->isCompleted() || $this->isPartiallyRefunded());
    }

    public function isExchanged()
    {
        return $this->exchange_currency_id || $this->exchange_rate != 1;
    }

    public function getBankDataAttribute()
    {
        if (!$this->routing_number) {
            return null;
        }

        return PaymentMethod::lookupBankData($this->routing_number);
    }

    public function getBankNameAttribute($bank_name)
    {
        if ($bank_name) {
            return $bank_name;
        }
        $bankData = $this->bank_data;

        return $bankData ? $bankData->name : null;
    }

    public function getLast4Attribute($value)
    {
        return $value ? str_pad($value, 4, '0', STR_PAD_LEFT) : null;
    }

    public static function calcStatusLabel($statusId, $statusName, $amount)
    {
        if ($statusId == PAYMENT_STATUS_PARTIALLY_REFUNDED) {
            return trans('texts.status_partially_refunded_amount', [
                'amount' => $amount,
            ]);
        } elseif ($statusId == PAYMENT_STATUS_PARTIALLY_PAID) {
            return trans('texts.status_partially_paid');
        } elseif ($statusId == PAYMENT_STATUS_ADVANCE_PAID) {
            return trans('texts.status_advance_paid');
        } else {
            return trans('texts.status_' . strtolower($statusName));
        }
    }

    public static function calcStatusClass($statusId)
    {
        return static::$statusClasses[$statusId];
    }

    public function statusClass()
    {
        return static::calcStatusClass($this->payment_status_id);
    }

    public function statusLabel()
    {
        $amount = $this->account->formatMoney($this->refunded, $this->client);

        return static::calcStatusLabel($this->payment_status_id, $this->payment_status->name, $amount);
    }

    public function invoiceJsonBackup()
    {
        $activity = Activity::where('payment_id', $this->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_PAYMENT)
            ->get(['json_backup'])
            ->first();

        if ($activity) {
            return $activity->json_backup;
        }

        return false;
    }
}

Payment::creating(function ($payment) {
//    do some stuff
});

Payment::created(function ($payment) {
    event(new PaymentWasCreatedEvent($payment));
});
