<?php

namespace App\Jobs\Vendor;

use App\Models\Eloquent;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchasePayment;
use App\Libraries\Utils;

class GeneratePurchaseStatementData
{
    public function __construct($vendor, $options, $contact = false)
    {
        $this->vendor = $vendor;
        $this->options = $options;
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $vendor = $this->vendor;
        $vendor->load('contacts');

        $account = $vendor->account;
        $account->load(['date_format', 'datetime_format']);

        $purchaseInvoice = new PurchaseInvoice();
        $purchaseInvoice->invoice_date = Utils::today();
        $purchaseInvoice->account = $account;
        $purchaseInvoice->vendor = $vendor;

        $purchaseInvoice->invoice_items = $this->getPurchaseInvoices();

        if ($this->options['show_payments']) {
            $payments = $this->getPurchasePayments($purchaseInvoice->invoice_items);
            $purchaseInvoice->invoice_items = $purchaseInvoice->invoice_items->merge($payments);
        }

        $purchaseInvoice->hidePrivateFields();

        return json_encode($purchaseInvoice);
    }

    private function getPurchaseInvoices()
    {
        $statusId = intval($this->options['status_id']);

        $purchaseInvoices = PurchaseInvoice::with(['vendor'])
            ->invoices()
            ->whereClientId($this->vendor->id)
            ->whereIsPublic(true)
            ->withArchived()
            ->orderBy('invoice_date', 'asc');

        if ($statusId == INVOICE_STATUS_PAID) {
            $purchaseInvoices->where('invoice_status_id', '=', INVOICE_STATUS_PAID);
        } elseif ($statusId == INVOICE_STATUS_UNPAID) {
            $purchaseInvoices->where('invoice_status_id', '!=', INVOICE_STATUS_PAID);
        }

        if ($statusId == INVOICE_STATUS_PAID || !$statusId) {
            $purchaseInvoices->where('invoice_date', '>=', $this->options['start_date'])
                ->where('invoice_date', '<=', $this->options['end_date']);
        }

        if ($this->contact) {
            $purchaseInvoices->whereHas('invitations', function ($query) {
                $query->where('contact_id', $this->contact->id);
            });
        }

        $purchaseInvoices = $purchaseInvoices->get();
        $data = collect();

        for ($i = 0; $i < $purchaseInvoices->count(); $i++) {
            $purchaseInvoice = $purchaseInvoices[$i];
            $item = new PurchaseInvoiceItem();
            $item->id = $purchaseInvoice->id;
            $item->product_key = $purchaseInvoice->invoice_number;
            $item->custom_value1 = $purchaseInvoice->invoice_date;
            $item->custom_value2 = $purchaseInvoice->due_date;
            $item->notes = $purchaseInvoice->amount;
            $item->cost = $purchaseInvoice->balance;
            $item->qty = 1;
            $item->invoice_item_type_id = 1;
            $data->push($item);
        }

        if ($this->options['show_aging']) {
            $aging = $this->getAging($purchaseInvoices);
            $data = $data->merge($aging);
        }

        return $data;
    }

    private function getPurchasePayments($purchaseInvoices)
    {
        $payments = PurchasePayment::with('invoice', 'payment_type')
            ->withArchived()
            ->whereClientId($this->vendor->id)
            //->excludeFailed()
            ->where('payment_date', '>=', $this->options['start_date'])
            ->where('payment_date', '<=', $this->options['end_date']);

        if ($this->contact) {
            $payments->whereIn('invoice_id', $purchaseInvoices->pluck('id'));
        }

        $payments = $payments->get();
        $data = collect();

        for ($i = 0; $i < $payments->count(); $i++) {
            $payment = $payments[$i];
            $item = new PurchaseInvoiceItem();
            $item->product_key = $payment->invoice->invoice_number;
            $item->custom_value1 = $payment->payment_date;
            $item->custom_value2 = $payment->present()->payment_type;
            $item->cost = $payment->getCompletedAmount();
            $item->invoice_item_type_id = 3;
            $item->notes = $payment->transaction_reference ?: ' ';
            $data->push($item);
        }

        return $data;
    }

    private function getAging($purchaseInvoices)
    {
        $data = collect();
        $ageGroups = [
            'age_group_0' => 0,
            'age_group_30' => 0,
            'age_group_60' => 0,
            'age_group_90' => 0,
            'age_group_120' => 0,
        ];

        foreach ($purchaseInvoices as $purchaseInvoice) {
            $age = $purchaseInvoice->present()->ageGroup;
            $ageGroups[$age] += $purchaseInvoice->getRequestedAmount();
        }

        $item = new PurchaseInvoiceItem();
        $item->product_key = $ageGroups['age_group_0'];
        $item->notes = $ageGroups['age_group_30'];
        $item->custom_value1 = $ageGroups['age_group_60'];
        $item->custom_value2 = $ageGroups['age_group_90'];
        $item->cost = $ageGroups['age_group_120'];
        $item->invoice_item_type_id = 4;
        $data->push($item);

        return $data;
    }
}
