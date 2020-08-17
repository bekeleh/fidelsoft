<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use App\Models\Traits\Inviteable;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * Class BillBillProposalInvitation.
 *
 * @property-read Account|null $account
 * @property-read VendorContact $contact
 * @property-read Proposal $proposal
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|BillProposalInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillProposalInvitation newQuery()
 * @method static Builder|BillProposalInvitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillProposalInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|BillProposalInvitation withTrashed()
 * @method static Builder|BillProposalInvitation withoutTrashed()
 * @mixin Eloquent
 */
class BillProposalInvitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;

    protected $table = 'Bill_proposal_invitations';
    protected $dates = ['deleted_at'];


    public function getEntityType()
    {
        return ENTITY_BILL_PROPOSAL_INVITATION;
    }

    public function getRoute()
    {
        return "/Bill_proposal_invitations/{$this->public_id}/edit";
    }

    public function proposal()
    {
        return $this->belongsTo('App\Models\Proposal')->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo('App\Models\VendorContact')->withTrashed();
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

BillProposalInvitation::creating(function ($invitation) {
    LookupBillProposalInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

BillProposalInvitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupBillProposalInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

BillProposalInvitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupBillProposalInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});
