<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Models\Traits\Inviteable;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class BillInvitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $bill_id
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
 * @property-read VendorContact|null $contact
 * @property-read Bill $invoice
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation newQuery()
 * @method static Builder|BillInvitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereEmailError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereOpenedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereSignatureBase64($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereSignatureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillInvitation whereViewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|BillInvitation withTrashed()
 * @method static Builder|BillInvitation withoutTrashed()
 * @mixin Eloquent
 */
class BillInvitation extends EntityModel
{
    use SoftDeletes;
    use Inviteable;

    protected $table = 'bill_invitations';
    protected $dates = ['deleted_at', 'updated_at', 'deleted_at'];
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
