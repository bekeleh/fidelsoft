<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $token
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupAccountToken newModelQuery()
 * @method static Builder|LookupAccountToken newQuery()
 * @method static Builder|LookupAccountToken query()
 * @method static Builder|LookupAccountToken whereCreatedAt($value)
 * @method static Builder|LookupAccountToken whereDeletedAt($value)
 * @method static Builder|LookupAccountToken whereId($value)
 * @method static Builder|LookupAccountToken whereLookupAccountId($value)
 * @method static Builder|LookupAccountToken whereToken($value)
 * @method static Builder|LookupAccountToken whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LookupAccountToken extends LookupModel
{

    protected $table = 'lookup_account_tokens';
    protected $fillable = [
        'lookup_account_id',
        'token',
    ];

}
