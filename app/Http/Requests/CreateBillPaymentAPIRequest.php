<?php

namespace App\Http\Requests;

use App\Models\Bill;

class CreateBillPaymentAPIRequest extends PaymentRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!$this->bill_id || !$this->amount) {
            return [
                'bill_id' => 'required|numeric|min:1',
                'amount' => 'required|numeric',
            ];
        }

        $this->bill = $bill = Bill::scope($this->bill_public_id ?: $this->bill_id)
            ->withArchived()
            ->bills()
            ->first();

        if (!$this->bill) {
            abort(404, 'Bill was not found');
        }

        $this->merge([
            'bill_id' => $bill->id,
            'vendor_id' => $bill->client->id,
        ]);

        $rules = [
            'amount' => 'required|numeric',
        ];

        if ($this->payment_type_id == PAYMENT_TYPE_CREDIT) {
            $rules['payment_type_id'] = 'has_vendor_credit:' . $bill->client->public_id . ',' . $this->amount;
        }

        return $rules;
    }
}
