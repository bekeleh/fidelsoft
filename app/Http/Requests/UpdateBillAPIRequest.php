<?php

namespace App\Http\Requests;

class UpdateBillAPIRequest extends BillRequest
{
    protected $entityType = ENTITY_BILL;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        if ($this->action == ACTION_ARCHIVE) {
            return [];
        }

        $invoiceId = $this->entity()->id;

        $rules = [
            'invoice_items' => 'valid_invoice_items',
            'invoice_number' => 'unique:bills,invoice_number,' . $invoiceId . ',id,account_id,' . $this->user()->account_id,
            'discount' => 'positive',
            //'invoice_date' => 'date',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        return $rules;
    }
}
