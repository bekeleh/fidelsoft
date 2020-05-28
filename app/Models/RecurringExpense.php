<?php

namespace App\Models;

//use App\Events\ExpenseWasCreated;
//use App\Events\ExpenseWasUpdated;
use App\Models\Traits\HasRecurrence;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use App\Libraries\Utils;

/**
 * Class Expense.
 */
class RecurringExpense extends EntityModel
{
    // Expenses
    use SoftDeletes;
    use PresentableTrait;
    use HasRecurrence;

    protected $dates = ['deleted_at'];

    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';


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
    //event(new ExpenseWasCreated($expense));
});

RecurringExpense::updating(function ($expense) {
    $expense->setNullValues();
});

RecurringExpense::updated(function ($expense) {
    //event(new ExpenseWasUpdated($expense));
});

RecurringExpense::deleting(function ($expense) {
    $expense->setNullValues();
});
