<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
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
