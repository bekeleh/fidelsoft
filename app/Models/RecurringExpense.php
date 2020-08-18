<?php

namespace App\Models;

use App\Models\EntityModel;
use App\Events\Expense\ExpenseWasCreatedEvent;
use App\Events\Expense\ExpenseWasUpdatedEvent;
use App\Models\Traits\HasRecurrence;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use App\Libraries\Utils;

/**
 * Class Expense.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_currency_id
 * @property int|null $expense_currency_id
 * @property int|null $expense_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $vendor_id
 * @property int|null $client_id
 * @property int $is_deleted
 * @property float $amount
 * @property string $private_notes
 * @property string $public_notes
 * @property int $should_be_invoiced
 * @property string|null $tax_name1
 * @property float $tax_rate1
 * @property string|null $tax_name2
 * @property float $tax_rate2
 * @property int $frequency_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $last_sent_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read ExpenseCategory|null $expense_category
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense newQuery()
 * @method static Builder|RecurringExpense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereExpenseCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereInvoiceCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereLastSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereShouldBeInvoiced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|RecurringExpense withTrashed()
 * @method static Builder|RecurringExpense withoutTrashed()
 * @mixin Eloquent
 */
class RecurringExpense extends EntityModel
{
    // Expenses
    use SoftDeletes;
    use PresentableTrait;
    use HasRecurrence;

    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';
    protected $table = 'expenses';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'client_id',
        'vendor_id',
        'expense_currency_id',
        //'invoice_currency_id',
        //'exchange_rate',
        'amount',
        'private_notes',
        'public_notes',
        'expense_category_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
        'should_be_invoiced',
        //'start_date',
        //'end_date',
        'frequency_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function expense_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
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

    public function getName()
    {
        if ($this->public_notes) {
            return Utils::truncateString($this->public_notes, 16);
        } else {
            return '#' . $this->public_id;
        }
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public function getRoute()
    {
        return "/recurring_expenses/{$this->public_id}/edit";
    }

    public function getEntityType()
    {
        return ENTITY_RECURRING_EXPENSE;
    }

    public function amountWithTax()
    {
        return $this->amount + Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }
}

RecurringExpense::creating(function ($expense) {
    $expense->setNullValues();
});

RecurringExpense::created(function ($expense) {
    event(new ExpenseWasCreatedEvent($expense));
});

RecurringExpense::updating(function ($expense) {
    $expense->setNullValues();
});

RecurringExpense::updated(function ($expense) {
    event(new ExpenseWasUpdatedEvent($expense));
});

RecurringExpense::deleting(function ($expense) {
    $expense->setNullValues();
});
