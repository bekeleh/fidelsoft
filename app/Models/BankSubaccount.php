<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class BankSubaccount.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $bank_account_id
 * @property int|null $public_id
 * @property string|null $account_name
 * @property string|null $account_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read BankAccount|null $bank_account
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount newQuery()
 * @method static Builder|BankSubaccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|BankSubaccount withTrashed()
 * @method static Builder|BankSubaccount withoutTrashed()
 * @mixin Eloquent
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
