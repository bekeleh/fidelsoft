<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
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
        return $this->belongsTo('App\Models\Account');
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
