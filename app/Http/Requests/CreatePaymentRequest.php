<?php

namespace App\Http\Requests;

use App\Models\Invoice;

class CreatePaymentRequest extends PaymentRequest
{
    protected $entityType = ENTITY_PAYMENT;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $input = $this->input();
        $this->invoice = $invoice = Invoice::scope($input['invoice'])
            ->withArchived()
            ->invoices()
            ->firstOrFail();

        $this->merge([
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client->id,
        ]);

        $rules = [
            'client' => 'required', // TODO: change to client_id once views are updated
            'invoice' => 'required', // TODO: change to invoice_id once views are updated
            'amount' => 'required|numeric',
            'payment_date' => 'required',
        ];

        if (!empty($input['payment_type_id']) && $input['payment_type_id'] == PAYMENT_TYPE_CREDIT) {
            $rules['payment_type_id'] = 'has_credit:' . $input['client'] . ',' . $input['amount'];
        }

        return $rules;
    }
}
