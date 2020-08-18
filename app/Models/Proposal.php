<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $proposal_template_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $private_notes
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|ProposalInvitation[] $invitations
 * @property-read int|null $invitations_count
 * @property-read Invoice|null $invoice
 * @property-read ProposalTemplate|null $proposal_template
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal newQuery()
 * @method static Builder|Proposal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereProposalTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Proposal withTrashed()
 * @method static Builder|Proposal withoutTrashed()
 * @mixin Eloquent
 */
class Proposal extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ProposalPresenter';

    protected $table = 'proposals';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'private_notes',
        'html',
        'css',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_PROPOSAL;
    }


    public function getRoute()
    {
        return "/proposals/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function invitations()
    {
        return $this->hasMany('App\Models\ProposalInvitation')->orderBy('proposal_invitations.contact_id');
    }

//    public function proposal_invitations()
//    {
//        return $this->hasMany('App\Models\ProposalInvitation')->orderBy('proposal_invitations.contact_id');
//    }

    public function proposal_template()
    {
        return $this->belongsTo('App\Models\ProposalTemplate')->withTrashed();
    }

    public function getDisplayName()
    {
        return $this->invoice->invoice_number;
    }

    public function getLink($forceOnsite = false, $forcePlain = false)
    {
        $invitation = $this->invitations->first();

        return $invitation->getLink('proposal', $forceOnsite, $forcePlain);
    }

    public function getHeadlessLink()
    {
        return sprintf('%s?phantomjs=true&phantomjs_secret=%s', $this->getLink(true, true), env('PHANTOMJS_SECRET'));
    }

    public function getFilename($extension = 'pdf')
    {
        $entityType = $this->getEntityType();

        return trans('texts.proposal') . '_' . $this->invoice->invoice_number . '.' . $extension;
    }

    public function getCustomMessageType()
    {
        if ($this->invoice->quote_invoice_id) {
            return CUSTOM_MESSAGE_APPROVED_PROPOSAL;
        } else {
            return CUSTOM_MESSAGE_UNAPPROVED_PROPOSAL;
        }
    }

}

Proposal::creating(function ($project) {
    $project->setNullValues();
});

Proposal::updating(function ($project) {
    $project->setNullValues();
});
