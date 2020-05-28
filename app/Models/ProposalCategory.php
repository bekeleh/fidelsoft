<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
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
