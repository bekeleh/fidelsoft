<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $contact_key
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupContact newModelQuery()
 * @method static Builder|LookupContact newQuery()
 * @method static Builder|LookupContact query()
 * @method static Builder|LookupContact whereContactKey($value)
 * @method static Builder|LookupContact whereCreatedAt($value)
 * @method static Builder|LookupContact whereDeletedAt($value)
 * @method static Builder|LookupContact whereId($value)
 * @method static Builder|LookupContact whereLookupAccountId($value)
 * @method static Builder|LookupContact whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LookupContact extends LookupModel
{
    protected $table = 'lookup_contacts';
    protected $fillable = [
        'lookup_account_id',
        'contact_key',
    ];

}
