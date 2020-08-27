<?php

namespace App\Http\Requests;

use App\Models\Bill;

class CreateBillPaymentRequest extends BillPaymentRequest
{
    protected $entityType = ENTITY_BILL_PAYMENT;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $input = $this->input();
        $this->bill = $bill = Bill::scope($input['bill'])
            ->withArchived()->bills()->firstOrFail();

        $this->merge([
            'bill_id' => $bill->id,
            'vendor_id' => $bill->vendor->id,
        ]);

        $rules = [
            'vendor' => 'required', // TODO: change to vendor_id once views are updated
            'bill' => 'required', // TODO: change to bill_id once views are updated
            'amount' => 'required|numeric',
            'payment_date' => 'required',
            'payment_status_id' => 'required',
            'payment_type_id' => 'required',
        ];

        if (!empty($input['payment_type_id']) && $input['payment_type_id'] == PAYMENT_TYPE_CREDIT) {
            $rules['payment_type_id'] = 'has_vendor_credit:' . $input['vendor'] . ',' . $input['amount'];
        }

        return $rules;
    }
}
