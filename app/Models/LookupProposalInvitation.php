<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $invitation_key
 * @property string|null $message_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $delete_at
 * @method static Builder|LookupProposalInvitation newModelQuery()
 * @method static Builder|LookupProposalInvitation newQuery()
 * @method static Builder|LookupProposalInvitation query()
 * @method static Builder|LookupProposalInvitation whereCreatedAt($value)
 * @method static Builder|LookupProposalInvitation whereDeleteAt($value)
 * @method static Builder|LookupProposalInvitation whereId($value)
 * @method static Builder|LookupProposalInvitation whereInvitationKey($value)
 * @method static Builder|LookupProposalInvitation whereLookupAccountId($value)
 * @method static Builder|LookupProposalInvitation whereMessageId($value)
 * @method static Builder|LookupProposalInvitation whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read LookupAccount|null $lookupAccount
 */
class LookupProposalInvitation extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_account_id',
        'invitation_key',
        'message_id',
    ];

    public static function updateInvitation($accountKey, $invitation)
    {
        if (! env('MULTI_DB_ENABLED')) {
            return;
        }

        if (! $invitation->message_id) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
                            ->firstOrFail();

        $lookupInvitation = LookupProposalInvitation::whereLookupAccountId($lookupAccount->id)
                                ->whereInvitationKey($invitation->invitation_key)
                                ->firstOrFail();

        $lookupInvitation->message_id = $invitation->message_id;
        $lookupInvitation->save();

        config(['database.default' => $current]);
    }

}
