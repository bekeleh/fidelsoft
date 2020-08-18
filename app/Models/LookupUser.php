<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LookupUser.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property int|null $user_id
 * @property string|null $email
 * @property string|null $confirmation_code
 * @property string|null $oauth_user_key
 * @property string|null $referral_code
 * @property string|null $create_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|LookupUser newModelQuery()
 * @method static Builder|LookupUser newQuery()
 * @method static Builder|LookupUser query()
 * @method static Builder|LookupUser whereConfirmationCode($value)
 * @method static Builder|LookupUser whereCreateAt($value)
 * @method static Builder|LookupUser whereDeletedAt($value)
 * @method static Builder|LookupUser whereEmail($value)
 * @method static Builder|LookupUser whereId($value)
 * @method static Builder|LookupUser whereLookupAccountId($value)
 * @method static Builder|LookupUser whereOauthUserKey($value)
 * @method static Builder|LookupUser whereReferralCode($value)
 * @method static Builder|LookupUser whereUpdatedAt($value)
 * @method static Builder|LookupUser whereUserId($value)
 * @mixin Eloquent
 * @property-read LookupAccount|null $lookupAccount
 */
class LookupUser extends LookupModel
{
    protected $table = 'lookup_users';
    protected $fillable = [
        'lookup_account_id',
        'email',
        'user_id',
        'confirmation_code',
        'oauth_user_key',
        'referral_code',
    ];

    public static function updateUser($accountKey, $user)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupUser = LookupUser::whereLookupAccountId($lookupAccount->id)
            ->whereUserId($user->id)
            ->firstOrFail();

        $lookupUser->email = $user->email;
        $lookupUser->confirmation_code = $user->confirmation_code ?: null;
        $lookupUser->oauth_user_key = ($user->oauth_provider_id && $user->oauth_user_id) ? ($user->oauth_provider_id . '-' . $user->oauth_user_id) : null;
        $lookupUser->referral_code = $user->referral_code;
        $lookupUser->save();

        config(['database.default' => $current]);
    }

    public static function validateField($field, $value, $user = false)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return true;
        }

        $current = config('database.default');
        $accountKey = $user ? $user->account->account_key : false;

        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupUser = LookupUser::where($field, $value)->first();

        if ($user) {
            $lookupAccount = LookupAccount::where('account_key', $accountKey)->firstOrFail();
            $isValid = !$lookupUser || ($lookupUser->lookup_account_id == $lookupAccount->id && $lookupUser->user_id == $user->id);
        } else {
            $isValid = !$lookupUser;
        }

        config(['database.default' => $current]);

        return $isValid;
    }

}
