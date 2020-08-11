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

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

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

    public static function selectOptions()
    {
        $categories = ExpenseCategory::where('account_id', null)->get();

        foreach (ExpenseCategory::scope()->get() as $category) {
            $categories->push($category);
        }

        foreach($categories as $category){
            $name = Str::snake(str_replace(' ', '_', $category->name));
            $categories->name = trans('texts.expense_category_' . $name);
        }

        return $categories->sortBy('name');
    }

}
