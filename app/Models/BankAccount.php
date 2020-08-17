<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $bank_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $username
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $app_version
 * @property int|null $ofx_version
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Bank|null $bank
 * @property-read Collection|BankSubaccount[] $bank_subaccounts
 * @property-read int|null $bank_subaccounts_count
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newQuery()
 * @method static Builder|BankAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereOfxVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|BankAccount withTrashed()
 * @method static Builder|BankAccount withoutTrashed()
 * @mixin Eloquent
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
