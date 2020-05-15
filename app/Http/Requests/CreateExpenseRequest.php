<?php

namespace App\Http\Requests;

use App\Libraries\Utils;
use App\Models\Client;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Vendor;

class CreateExpenseRequest extends ExpenseRequest
{
    protected $entityType = ENTITY_EXPENSE;

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_EXPENSE);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];

        $rules['vendor_id'] = 'required|unique:expenses,vendor_id,' . $this->id . ',id,amount,' . $this->amount . ',expense_date,' . $this->expense_date . ',client_id,' . $this->client_id . ',account_id,' . $this->account_id;
        $rules['client_id'] = 'required|unique:expenses,client_id,' . $this->id . ',id,amount,' . $this->amount . ',expense_date,' . $this->expense_date . ',vendor_id,' . $this->vendor_id . ',account_id,' . $this->account_id;
        $rules['expense_category_id'] = 'required|numeric|exists:expense_categories,id';
        $rules['amount'] = 'required';
        $rules['expense_date'] = 'required';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['vendor_id'])) {
            $input['vendor_id'] = filter_var($input['vendor_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (!empty($input['expense_category_id'])) {
            $input['expense_category_id'] = filter_var($input['expense_category_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (!empty($input['client_id'])) {
            $input['client_id'] = filter_var($input['client_id'], FILTER_SANITIZE_NUMBER_INT);
        }

        $this->replace($input);
    }

    public function validationData()
    {
        $input = $this->input();
        if (!empty($input['vendor_id'])) {
            $input['vendor_id'] = Vendor::getPrivateId($input['vendor_id']);
        }
        if (!empty($input['expense_category_id'])) {
            $input['expense_category_id'] = ExpenseCategory::getPrivateId($input['expense_category_id']);
        }
        if (!empty($input['client_id'])) {
            $input['client_id'] = Client::getPrivateId($input['client_id']);
        }
        if (!empty($input['vendor_id']) && !empty($input['expense_category_id']) && !empty($input['client_id'])) {
            $this->request->add([
                'vendor_id' => $input['vendor_id'],
                'expense_category_id' => $input['expense_category_id'],
                'client_id' => $input['client_id'],
                'amount' => sprintf("%.2f", $input['amount']),
                'expense_date' => Utils::toSqlDate($input['expense_date']),
                'account_id' => User::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
