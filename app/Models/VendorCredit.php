<?php

namespace App\Models;

use App\Models\EntityModel;
use App\Events\Vendor\VendorCreditWasCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class VendorCredit.
 */
class VendorCredit extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $table = 'vendor_credits';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $presenter = 'App\Ninja\Presenters\VendorCreditPresenter';


    protected $fillable = [
        'public_notes',
        'private_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function Bill()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
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
        return "/vendor_credits/{$this->public_id}";
    }

    public function getEntityType()
    {
        return ENTITY_VENDOR_CREDIT;
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

VendorCredit::creating(function ($credit) {
});

VendorCredit::created(function ($credit) {
    event(new VendorCreditWasCreated($credit));
});
