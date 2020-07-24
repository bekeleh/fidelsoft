<?php

namespace App\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasVoided;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;
use Laracasts\Presenter\PresentableTrait;


class Payment extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

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
        return $this->belongsTo('App\Models\Account');
    }


    public function contact()
    {
        return $this->belongsTo('App\Models\Contact')->withTrashed();
    }


    public function account_gateway()
    {
        return $this->belongsTo('App\Models\AccountGateway')->withTrashed();
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

            Event::fire(new PaymentWasRefunded($this, $refund_change));
        }

        return true;
    }

    public function markVoided()
    {
        if ($this->isVoided() || $this->isPartiallyRefunded() || $this->isRefunded()) {
            return false;
        }

        Event::fire(new PaymentWasVoided($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
    }

    public function markComplete()
    {
        $this->payment_status_id = PAYMENT_STATUS_COMPLETED;
        $this->save();
        Event::fire(new PaymentCompleted($this));
    }

    public function markFailed($failureMessage = '')
    {
        $this->payment_status_id = PAYMENT_STATUS_FAILED;
        $this->gateway_error = $failureMessage;
        $this->save();
        Event::fire(new PaymentFailed($this));
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

        return $activity->json_backup;
    }
}

Payment::creating(function ($payment) {
//    do some stuff
});

Payment::created(function ($payment) {
    event(new PaymentWasCreated($payment));
});
