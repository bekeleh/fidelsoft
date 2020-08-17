<?php

namespace App\Models;

use App\Models\Common\EntityModel;
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
 * @property string|null $name
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory newQuery()
 * @method static Builder|ProposalCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ProposalCategory withTrashed()
 * @method static Builder|ProposalCategory withoutTrashed()
 * @mixin Eloquent
 */
class ProposalCategory extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;


    protected $dates = ['deleted_at'];


    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_PROPOSAL_CATEGORY;
    }

    public function getRoute()
    {
        return "/proposals/categories/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
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
