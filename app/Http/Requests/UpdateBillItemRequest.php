<?php

namespace App\Http\Requests;

use App\Models\Vendor;

class UpdateBillItemRequest extends BillItemRequest
{
    protected $entityType = ENTITY_BILL_ITEM;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        $billId = $this->entity()->id;

        $rules = [
            'client' => 'required',
            'invoice_items' => 'valid_bill_items',
            'invoice_number' => 'required|unique:bills,invoice_number,' . $billId . ',id,account_id,' . $this->user()->account_id,
            'discount' => 'positive',
            'bill_date' => 'required',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        if ($this->user()->account->client_number_counter) {
            $vendorId = Vendor::getPrivateId(request()->input('client')['public_id']);
            $rules['client.id_number'] = 'unique:vendors,id_number,' . $vendorId . ',id,account_id,' . $this->user()->account_id;
        }

        /* There's a problem parsing the dates
        if (Request::get('is_recurring') && Request::get('start_date') && Request::get('end_date')) {
            $rules['end_date'] = 'after' . Request::get('start_date');
        }
        */

        return $rules;
    }
}
