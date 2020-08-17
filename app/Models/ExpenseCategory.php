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
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Expense $expense
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newQuery()
 * @method static Builder|ExpenseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ExpenseCategory withTrashed()
 * @method static Builder|ExpenseCategory withoutTrashed()
 * @mixin Eloquent
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
