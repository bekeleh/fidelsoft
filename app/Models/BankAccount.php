<?php

namespace App\Models;

use Crypt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount.
 */
class BankAccount extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'app_version',
        'ofx_version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_BANK_ACCOUNT;
    }

    public function getRoute()
    {
        return "/bank_accounts/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        if (isset($this->username)) {
            return Crypt::decrypt($this->username);
        }
    }

    /**
     * @param $value
     */
    public function setUsername($value)
    {
        $this->username = Crypt::encrypt($value);
    }

    /**
     * @return BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }

    /**
     * @return HasMany
     */
    public function bank_subaccounts()
    {
        return $this->hasMany('App\Models\BankSubaccount');
    }
}
