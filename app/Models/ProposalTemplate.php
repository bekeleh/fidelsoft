<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $private_notes
 * @property string|null $name
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate newQuery()
 * @method static Builder|ProposalTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ProposalTemplate withTrashed()
 * @method static Builder|ProposalTemplate withoutTrashed()
 * @mixin Eloquent
 */
class ProposalTemplate extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'private_notes',
        'html',
        'css',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    protected $presenter = 'App\Ninja\Presenters\ProposalTemplatePresenter';

    public function getEntityType()
    {
        return ENTITY_PROPOSAL_TEMPLATE;
    }

    public function getRoute()
    {
        return "/proposals/templates/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}

/*
Proposal::creating(function ($project) {
    $project->setNullValues();
});

Proposal::updating(function ($project) {
    $project->setNullValues();
});
*/
