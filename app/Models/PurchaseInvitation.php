<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PurchaseInvitation.
 */
class PurchaseInvitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;

    protected $appends = [];
    protected $casts = [];
    protected $dates = ['deleted_at', 'updated_at', 'deleted_at'];
    protected $hidden = [];

    public function getEntityType()
    {
        return ENTITY_PURCHASE_INVITATION;
    }

    public function getRoute()
    {
        return "/invitations/{$this->public_id}/edit";
    }

    public function purchase_invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice')->withTrashed();
    }

    public function vendor_contact()
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

PurchaseInvitation::creating(function ($invitation) {
    LookupPurchaseInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

PurchaseInvitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupPurchaseInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

PurchaseInvitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupPurchaseInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});
