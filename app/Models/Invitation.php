<?php

namespace App\Models;

use App\Models\EntityModel;
use App\Libraries\Utils;
use App\Models\Traits\Inviteable;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class Invitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $invoice_id
 * @property string|null $message_id
 * @property string|null $invitation_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $transaction_reference
 * @property string|null $sent_date
 * @property string|null $viewed_date
 * @property string|null $opened_date
 * @property string|null $email_error
 * @property string|null $signature_base64
 * @property string|null $signature_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Contact|null $contact
 * @property-read Invoice|null $invoice
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newQuery()
 * @method static Builder|Invitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereEmailError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereOpenedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSignatureBase64($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSignatureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereViewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Invitation withTrashed()
 * @method static Builder|Invitation withoutTrashed()
 * @mixin Eloquent
 */
class Invitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;

    protected $table = 'invitations';

    protected $dates = ['deleted_at'];
    protected $hidden = [];

    public function getEntityType()
    {
        return ENTITY_INVITATION;
    }

    public function getRoute()
    {
        return "/invitations/{$this->public_id}/edit";
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
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

    public function signatureDiv()
    {
        if (!$this->signature_base64) {
            return false;
        }

        return sprintf('<img src="data:image/svg+xml;base64,%s"></img><p/>%s: %s', $this->signature_base64, trans('texts.signed'), Utils::fromSqlDateTime($this->signature_date));
    }
}

Invitation::creating(function ($invitation) {
    LookupInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

Invitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

Invitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});
