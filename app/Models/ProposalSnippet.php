<?php

namespace App\Models;

use App\Models\EntityModel;
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
 * @property int|null $proposal_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $private_notes
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read ProposalCategory|null $proposal_category
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet newQuery()
 * @method static Builder|ProposalSnippet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereProposalCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ProposalSnippet withTrashed()
 * @method static Builder|ProposalSnippet withoutTrashed()
 * @mixin Eloquent
 */
class ProposalSnippet extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $dates = ['deleted_at'];


    protected $fillable = [
        'name',
        'icon',
        'private_notes',
        'proposal_category_id',
        'html',
        'css',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $presenter = 'App\Ninja\Presenters\ProposalSnippetPresenter';


    public function getEntityType()
    {
        return ENTITY_PROPOSAL_SNIPPET;
    }

    public function getRoute()
    {
        return "/proposals/snippets/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function proposal_category()
    {
        return $this->belongsTo('App\Models\ProposalCategory')->withTrashed();
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
