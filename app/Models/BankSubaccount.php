<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankSubaccount.
 */
class BankSubaccount extends EntityModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public function getEntityType()
    {
        return ENTITY_BANK_SUBACCOUNT;
    }


    public function bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount');
    }
}
