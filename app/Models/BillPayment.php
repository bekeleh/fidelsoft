<?php

namespace App\Models;

use App\Events\Purchase\BillPaymentCompletedEvent;
use App\Events\Purchase\BillPaymentFailedEvent;
use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Events\Purchase\BillPaymentWasRefundedEvent;
use App\Events\Purchase\BillPaymentWasVoidedEvent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;
use Laracasts\Presenter\PresentableTrait;


class BillPayment extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\BillPaymentPresenter';

    protected $table = 'bill_payments';

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

    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['deleted_at'];

    public function getEntityType()
    {
        return ENTITY_BILL_PAYMENT;
    }

    public function getRoute()
    {
        return "/bill_payments/{$this->public_id}";
    }

    public function bill()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
    }


    public function bill_invitation()
    {
        return $this->belongsTo('App\Models\BillInvitation');
    }


    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
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
        return $this->belongsTo('App\Models\VendorContact')->withTrashed();
    }


    public function account_gateway()
    {
        return $this->belongsTo('App\Models\Common\AccountGateway')->withTrashed();
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\BillPaymentType');
    }


    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod');
    }

    public function payment_status()
    {
        return $this->belongsTo('App\Models\BillPaymentStatus');
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

            Event::fire(new BillPaymentWasRefundedEvent($this, $refund_change));
        }

        return true;
    }

    public function markVoided()
    {
        if ($this->isVoided() || $this->isPartiallyRefunded() || $this->isRefunded()) {
            return false;
        }

        Event::fire(new BillPaymentWasVoidedEvent($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
    }

    public function markComplete()
    {
        $this->payment_status_id = PAYMENT_STATUS_COMPLETED;
        $this->save();
        Event::fire(new BillPaymentCompletedEvent($this));
    }

    public function markFailed($failureMessage = '')
    {
        $this->payment_status_id = PAYMENT_STATUS_FAILED;
        $this->gateway_error = $failureMessage;
        $this->save();
        Event::fire(new BillPaymentFailedEvent($this));
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
        $amount = $this->account->formatMoney($this->refunded, $this->vendor);

        return static::calcStatusLabel($this->payment_status_id, $this->payment_status->name, $amount);
    }

    public function billJsonBackup()
    {
        $activity = Activity::where('bill_payment_id', $this->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_PAYMENT)
            ->get(['json_backup'])
            ->first();

        return $activity->json_backup;
    }
}

BillPayment::creating(function ($payment) {
});

BillPayment::created(function ($payment) {
    event(new BillPaymentWasCreatedEvent($payment));
});
