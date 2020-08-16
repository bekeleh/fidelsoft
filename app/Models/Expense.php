<?php

namespace App\Models;

use App\Events\ExpenseWasCreatedEvent;
use App\Events\ExpenseWasUpdatedEvent;
use App\Libraries\Utils;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Expense.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property int|null $invoice_currency_id
 * @property int|null $expense_currency_id
 * @property int|null $expense_category_id
 * @property int|null $payment_type_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $vendor_id
 * @property string|null $transaction_id
 * @property int|null $recurring_expense_id
 * @property int|null $bank_id
 * @property int $is_deleted
 * @property float $amount
 * @property float $exchange_rate
 * @property string|null $expense_date
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property int $should_be_invoiced
 * @property string|null $tax_name1
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property float|null $tax_rate1
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property string|null $payment_date
 * @property string|null $transaction_reference
 * @property int|null $invoice_documents
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Collection|Document[] $documents
 * @property-read int|null $documents_count
 * @property-read ExpenseCategory|null $expense_category
 * @property-read Invoice|null $invoice
 * @property-read PaymentType|null $payment_type
 * @property-read RecurringExpense|null $recurring_expense
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|Expense bankId($bankdId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static Builder|Expense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereRecurringExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereShouldBeInvoiced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Expense withTrashed()
 * @method static Builder|Expense withoutTrashed()
 * @mixin Eloquent
 */
class Expense extends EntityModel
{
    // Expenses
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'client_id',
        'vendor_id',
        'expense_currency_id',
        'expense_date',
        'invoice_currency_id',
        'amount',
        'foreign_amount',
        'exchange_rate',
        'private_notes',
        'public_notes',
        'bank_id',
        'transaction_id',
        'expense_category_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
        'payment_date',
        'payment_type_id',
        'transaction_reference',
        'invoice_documents',
        'should_be_invoiced',
        'custom_value1',
        'custom_value2',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function getImportColumns()
    {
        return [
            'client',
            'vendor',
            'amount',
            'public_notes',
            'private_notes',
            'expense_category',
            'expense_date',
            'payment_type',
            'payment_date',
            'transaction_reference',
        ];
    }

    public static function getImportMap()
    {
        return [
            'amount|total' => 'amount',
            'category' => 'expense_category',
            'client' => 'client',
            'vendor' => 'vendor',
            'notes|details^private' => 'public_notes',
            'notes|details^public' => 'private_notes',
            'date^payment' => 'expense_date',
            'payment type' => 'payment_type',
            'payment date' => 'payment_date',
            'reference' => 'transaction_reference',
        ];
    }

    public function getEntityType()
    {
        return ENTITY_EXPENSE;
    }

    public function getRoute()
    {
        return "/expenses/{$this->public_id}/edit";
    }

    public function expense_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document')->orderBy('id');
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\PaymentType');
    }

    public function recurring_expense()
    {
        return $this->belongsTo('App\Models\RecurringExpense');
    }


    public function getName()
    {
        if ($this->transaction_id) {
            return $this->transaction_id;
        } elseif ($this->public_notes) {
            return Utils::truncateString($this->public_notes, 16);
        } else {
            return '#' . $this->public_id;
        }
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public function isExchanged()
    {
        return $this->invoice_currency_id != $this->expense_currency_id || $this->exchange_rate != 1;
    }

    public function isPaid()
    {
        return $this->payment_date || $this->payment_type_id;
    }

    public function convertedAmount()
    {
        return round($this->amount * $this->exchange_rate, 2);
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('converted_amount', $this->visible)) {
            $array['converted_amount'] = $this->convertedAmount();
        }

        return $array;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeBankId($query, $bankdId = null)
    {
        if ($bankdId) {
            $query->where('bank_id', $bankdId);
        }

        return $query;
    }

    public function amountWithTax()
    {
        return $this->amount + $this->taxAmount();
    }

    public function taxAmount()
    {
        return Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }

    public static function getStatuses($entityType = false)
    {
        $statuses = [];
        $statuses[EXPENSE_STATUS_LOGGED] = trans('texts.logged');
        $statuses[EXPENSE_STATUS_PENDING] = trans('texts.pending');
        $statuses[EXPENSE_STATUS_INVOICED] = trans('texts.invoiced');
        $statuses[EXPENSE_STATUS_BILLED] = trans('texts.billed');
        $statuses[EXPENSE_STATUS_PAID] = trans('texts.paid');
        $statuses[EXPENSE_STATUS_UNPAID] = trans('texts.unpaid');

        return $statuses;
    }

    public static function calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate)
    {
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                $label = 'invoiced';
            } else {
                $label = 'billed';
            }
        } elseif ($shouldBeInvoiced) {
            $label = 'pending';
        } else {
            $label = 'logged';
        }

        $label = trans("texts.{$label}");

        if ($paymentDate) {
            $label = trans('texts.paid') . ' | ' . $label;
        }

        return $label;
    }

    public static function calcStatusClass($shouldBeInvoiced, $invoiceId, $balance)
    {
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                return 'default';
            } else {
                return 'success';
            }
        } elseif ($shouldBeInvoiced) {
            return 'warning';
        } else {
            return 'primary';
        }
    }

    public function statusClass()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusClass($this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public function statusLabel()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusLabel($this->should_be_invoiced, $this->invoice_id, $balance, $this->payment_date);
    }
}

Expense::creating(function ($expense) {
    $expense->setNullValues();
});

Expense::created(function ($expense) {
    event(new ExpenseWasCreatedEvent($expense));
});

Expense::updating(function ($expense) {
    $expense->setNullValues();
});

Expense::updated(function ($expense) {
    event(new ExpenseWasUpdatedEvent($expense));
});

Expense::deleting(function ($expense) {
    $expense->setNullValues();
});
