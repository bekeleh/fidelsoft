<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LookupInvitation.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $invitation_key
 * @property string|null $message_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupInvitation newModelQuery()
 * @method static Builder|LookupInvitation newQuery()
 * @method static Builder|LookupInvitation query()
 * @method static Builder|LookupInvitation whereCreatedAt($value)
 * @method static Builder|LookupInvitation whereDeletedAt($value)
 * @method static Builder|LookupInvitation whereId($value)
 * @method static Builder|LookupInvitation whereInvitationKey($value)
 * @method static Builder|LookupInvitation whereLookupAccountId($value)
 * @method static Builder|LookupInvitation whereMessageId($value)
 * @method static Builder|LookupInvitation whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LookupInvitation extends LookupModel
{
    protected $table = 'lookup_invitations';
    protected $fillable = [
        'lookup_account_id',
        'invitation_key',
        'message_id',
    ];

    public static function updateInvitation($accountKey, $invitation)
    {
        if (!env('MULTI_DB_ENABLED')) {
            return;
        }

        if (!$invitation->message_id) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupInvitation = LookupInvitation::whereLookupAccountId($lookupAccount->id)
            ->whereInvitationKey($invitation->invitation_key)
            ->firstOrFail();

        $lookupInvitation->message_id = $invitation->message_id;
        $lookupInvitation->save();

        config(['database.default' => $current]);
    }

}
