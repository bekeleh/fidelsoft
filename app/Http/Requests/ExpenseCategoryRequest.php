<?php

namespace App\Http\Requests;

class ExpenseCategoryRequest extends EntityRequest
{
    protected $entityType = ENTITY_EXPENSE_CATEGORY;

    public function authorize()
    {
        return true;
    }

}
