<?php

namespace App\Models;

use App\Events\BillCreditWasCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class BillBillCredit.
 */
class BillCredit extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;


    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $presenter = 'App\Ninja\Presenters\BillCreditPresenter';


    protected $fillable = [
        'public_notes',
        'private_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    public function getName()
    {
        return '';
    }

    public function getRoute()
    {
        return "/BILL_CREDITs/{$this->public_id}/edit";
    }

    public function getEntityType()
    {
        return ENTITY_BILL_CREDIT;
    }

    public function apply($amount)
    {
        if ($amount > $this->balance) {
            $applied = $this->balance;
            $this->balance = 0;
        } else {
            $applied = $amount;
            $this->balance = $this->balance - $amount;
        }

        $this->save();

        return $applied;
    }
}

BillCredit::creating(function ($credit) {
});

BillCredit::created(function ($credit) {
    event(new BillCreditWasCreated($credit));
});
