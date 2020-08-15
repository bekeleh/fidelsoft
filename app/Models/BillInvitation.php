<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BillInvitation.
 */
class BillInvitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;

    protected $dates = ['deleted_at', 'updated_at'];
    protected $hidden = ['deleted_at'];

    public function getEntityType()
    {
        return ENTITY_BILL_INVITATION;
    }

    public function getRoute()
    {
        return "/invitations/{$this->public_id}/edit";
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
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
        return $this->belongsTo('App\Models\Account');
    }

    public function signatureDiv()
    {
        if (!$this->signature_base64) {
            return false;
        }

        return sprintf('<img src="data:image/svg+xml;base64,%s"></img><p/>%s: %s', $this->signature_base64, trans('texts.signed'), Utils::fromSqlDateTime($this->signature_date));
    }
}

BillInvitation::creating(function ($invitation) {
    LookupBillInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

BillInvitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupBillInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

BillInvitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupBillInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});
