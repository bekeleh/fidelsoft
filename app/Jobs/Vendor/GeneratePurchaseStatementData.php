<?php

namespace App\Jobs\Vendor;

use App\Models\Eloquent;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillPayment;
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

        $Bill = new Bill();
        $Bill->invoice_date = Utils::today();
        $Bill->account = $account;
        $Bill->vendor = $vendor;

        $Bill->invoice_items = $this->getbills();

        if ($this->options['show_payments']) {
            $payments = $this->getPurchasePayments($Bill->invoice_items);
            $Bill->invoice_items = $Bill->invoice_items->merge($payments);
        }

        $Bill->hidePrivateFields();

        return json_encode($Bill);
    }

    private function getbills()
    {
        $statusId = intval($this->options['status_id']);

        $bills = Bill::with(['vendor'])
            ->invoices()
            ->whereClientId($this->vendor->id)
            ->whereIsPublic(true)
            ->withArchived()
            ->orderBy('invoice_date', 'asc');

        if ($statusId == INVOICE_STATUS_PAID) {
            $bills->where('invoice_status_id', '=', INVOICE_STATUS_PAID);
        } elseif ($statusId == INVOICE_STATUS_UNPAID) {
            $bills->where('invoice_status_id', '!=', INVOICE_STATUS_PAID);
        }

        if ($statusId == INVOICE_STATUS_PAID || !$statusId) {
            $bills->where('invoice_date', '>=', $this->options['start_date'])
                ->where('invoice_date', '<=', $this->options['end_date']);
        }

        if ($this->contact) {
            $bills->whereHas('invitations', function ($query) {
                $query->where('contact_id', $this->contact->id);
            });
        }

        $bills = $bills->get();
        $data = collect();

        for ($i = 0; $i < $bills->count(); $i++) {
            $Bill = $bills[$i];
            $item = new BillItem();
            $item->id = $Bill->id;
            $item->product_key = $Bill->invoice_number;
            $item->custom_value1 = $Bill->invoice_date;
            $item->custom_value2 = $Bill->due_date;
            $item->notes = $Bill->amount;
            $item->cost = $Bill->balance;
            $item->qty = 1;
            $item->invoice_item_type_id = 1;
            $data->push($item);
        }

        if ($this->options['show_aging']) {
            $aging = $this->getAging($bills);
            $data = $data->merge($aging);
        }

        return $data;
    }

    private function getPurchasePayments($bills)
    {
        $payments = BillPayment::with('invoice', 'payment_type')
            ->withArchived()
            ->whereClientId($this->vendor->id)
            //->excludeFailed()
            ->where('payment_date', '>=', $this->options['start_date'])
            ->where('payment_date', '<=', $this->options['end_date']);

        if ($this->contact) {
            $payments->whereIn('invoice_id', $bills->pluck('id'));
        }

        $payments = $payments->get();
        $data = collect();

        for ($i = 0; $i < $payments->count(); $i++) {
            $payment = $payments[$i];
            $item = new BillItem();
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

    private function getAging($bills)
    {
        $data = collect();
        $ageGroups = [
            'age_group_0' => 0,
            'age_group_30' => 0,
            'age_group_60' => 0,
            'age_group_90' => 0,
            'age_group_120' => 0,
        ];

        foreach ($bills as $Bill) {
            $age = $Bill->present()->ageGroup;
            $ageGroups[$age] += $Bill->getRequestedAmount();
        }

        $item = new BillItem();
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
