<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $db_server_id
 * @property int|null $company_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read DbServer|null $dbServer
 * @property-read LookupAccount $lookupAccount
 * @method static Builder|LookupCompany newModelQuery()
 * @method static Builder|LookupCompany newQuery()
 * @method static Builder|LookupCompany query()
 * @method static Builder|LookupCompany whereCompanyId($value)
 * @method static Builder|LookupCompany whereCreatedAt($value)
 * @method static Builder|LookupCompany whereDbServerId($value)
 * @method static Builder|LookupCompany whereDeletedAt($value)
 * @method static Builder|LookupCompany whereId($value)
 * @method static Builder|LookupCompany whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LookupCompany extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'db_server_id',
        'company_id',
    ];

    public function dbServer()
    {
        return $this->belongsTo('App\Models\DbServer');
    }

    public function getDbServer()
    {
        return $this->dbServer->name;
    }

}
