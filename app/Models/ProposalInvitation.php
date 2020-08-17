<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use App\Models\Traits\Inviteable;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class ProposalInvitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $proposal_id
 * @property string|null $invitation_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $sent_date
 * @property string|null $viewed_date
 * @property string|null $opened_date
 * @property string|null $message_id
 * @property string|null $email_error
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Contact|null $contact
 * @property-read Proposal|null $proposal
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation newQuery()
 * @method static Builder|ProposalInvitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereEmailError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereOpenedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereViewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ProposalInvitation withTrashed()
 * @method static Builder|ProposalInvitation withoutTrashed()
 * @mixin Eloquent
 */
class ProposalInvitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;


    protected $dates = ['deleted_at'];


    public function getEntityType()
    {
        return ENTITY_PROPOSAL_INVITATION;
    }

    public function proposal()
    {
        return $this->belongsTo('App\Models\Proposal')->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo('App\Models\Contact')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }
}

ProposalInvitation::creating(function ($invitation) {
    LookupProposalInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

ProposalInvitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupProposalInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

ProposalInvitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupProposalInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});
