<?php

namespace App\Ninja\Repositories;

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\ExpenseCategory';
    }

    public function all()
    {
        return ExpenseCategory::scope()->get();
    }

    public function find($filter = null)
    {
        $query = DB::table('expense_categories')
//             ->join('expense_categories.id,'=',)
//            ->where('expense_categories.account_id', '=', Auth::user()->account_id)
//            ->where('expense_categories.deleted_at', '=', null)
            ->select(
                'expense_categories.name as category',
                'expense_categories.public_id',
                'expense_categories.user_id',
                'expense_categories.notes',
                'expense_categories.created_by',
                'expense_categories.updated_by',
                'expense_categories.deleted_by',
                'expense_categories.created_at',
                'expense_categories.updated_at',
                'expense_categories.deleted_at',
                'expense_categories.is_deleted'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('expense_categories.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_EXPENSE_CATEGORY);

        return $query;
    }

    public function save($data, $expenseCategory = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($expenseCategory) {
            $expenseCategory->updated_by = Auth::user()->username;

        } elseif ($publicId) {
            $expenseCategory = ExpenseCategory::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in expense category repo save');
        } else {
            $expenseCategory = ExpenseCategory::createNew();
            $expenseCategory->created_by = Auth::user()->name;
        }

        $expenseCategory->fill($data);

        $expenseCategory->save();

        return $expenseCategory;
    }
}
