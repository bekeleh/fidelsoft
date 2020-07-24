<?php

namespace App\Http\Requests;

use App\Models\Client;

class UpdatePurchaseInvoiceRequest extends InvoiceRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        $invoiceId = $this->entity()->id;

        $rules = [
            'vendor' => 'required',
            'invoice_items' => 'valid_invoice_items',
            'invoice_number' => 'required|unique:purchase_invoices,invoice_number,' . $invoiceId . ',id,account_id,' . $this->user()->account_id,
            'discount' => 'positive',
            'invoice_date' => 'required',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        if ($this->user()->account->vendor_number_counter) {
            $vendorId = Client::getPrivateId(request()->input('vendor')['public_id']);
            $rules['vendor.id_number'] = 'unique:vendors,id_number,' . $vendorId . ',id,account_id,' . $this->user()->account_id;
        }

        /* There's a problem parsing the dates
        if (Request::get('is_recurring') && Request::get('start_date') && Request::get('end_date')) {
            $rules['end_date'] = 'after' . Request::get('start_date');
        }
        */

        return $rules;
    }
}
