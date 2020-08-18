<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_company_id
 * @property string|null $account_key
 * @property string|null $subdomain
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount $lookupAccount
 * @property-read LookupCompany|null $lookupCompany
 * @method static Builder|LookupAccount newModelQuery()
 * @method static Builder|LookupAccount newQuery()
 * @method static Builder|LookupAccount query()
 * @method static Builder|LookupAccount whereAccountKey($value)
 * @method static Builder|LookupAccount whereCreatedAt($value)
 * @method static Builder|LookupAccount whereDeletedAt($value)
 * @method static Builder|LookupAccount whereId($value)
 * @method static Builder|LookupAccount whereLookupCompanyId($value)
 * @method static Builder|LookupAccount whereSubdomain($value)
 * @method static Builder|LookupAccount whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LookupAccount extends LookupModel
{

    protected $table = 'lookup_accounts';
    protected $fillable = [
        'lookup_company_id',
        'account_key',
    ];

    public function lookupCompany()
    {
        return $this->belongsTo('App\Models\LookupCompany');
    }

    public static function createAccount($accountKey, $companyId)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $server = DbServer::whereName($current)->firstOrFail();
        $lookupCompany = LookupCompany::whereDbServerId($server->id)
            ->whereCompanyId($companyId)->first();

        if (!$lookupCompany) {
            $lookupCompany = LookupCompany::create([
                'db_server_id' => $server->id,
                'company_id' => $companyId,
            ]);
        }

        LookupAccount::create([
            'lookup_company_id' => $lookupCompany->id,
            'account_key' => $accountKey,
        ]);

        static::setDbServer($current);
    }

    public function getDbServer()
    {
        return $this->lookupCompany->dbServer->name;
    }

    public static function updateAccount($accountKey, $account)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupAccount->subdomain = $account->subdomain ?: null;
        $lookupAccount->save();

        config(['database.default' => $current]);
    }

    public static function validateField($field, $value, $account = false)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return true;
        }

        $current = config('database.default');

        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::where($field, '=', $value)->first();

        if ($account) {
            $isValid = !$lookupAccount || ($lookupAccount->account_key == $account->account_key);
        } else {
            $isValid = !$lookupAccount;
        }

        config(['database.default' => $current]);

        return $isValid;
    }
}
