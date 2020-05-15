<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class ExpenseCategory extends EntityModel
{
    // Expense Categories
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\EntityPresenter';

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    public function getRoute()
    {
        return "/expense_categories/{$this->public_id}/edit";
    }

    public function expense()
    {
        return $this->belongsTo('App\Models\Expense');
    }

}
