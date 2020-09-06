<?php

namespace App\Ninja\Reports;

use App\Models\Vendor;
use App\Models\TaxRate;
use Barracuda\ArchiveStream\Archive;
use Illuminate\Support\Facades\Auth;

class BillReport extends AbstractReport
{
    public function getColumns()
    {
        $columns = [
            'vendor' => [],
            'invoice_number' => [],
            'bill_date' => [],
            'amount' => [],
            'status' => [],
            'payment_date' => [],
            'paid' => [],
            'method' => [],
            'due_date' => ['columnSelector-false'],
            'po_number' => ['columnSelector-false'],
            'private_notes' => ['columnSelector-false'],
            'vat_number' => ['columnSelector-false'],
            'user' => ['columnSelector-false'],
        ];

        if (TaxRate::scope()->count()) {
            $columns['tax'] = ['columnSelector-false'];
        }

        $account = auth()->user()->account;

        if ($account->customLabel('invoice_text1')) {
            $columns[$account->present()->customLabel('invoice_text1')] = ['columnSelector-false', 'custom'];
        }
        if ($account->customLabel('invoice_text1')) {
            $columns[$account->present()->customLabel('invoice_text2')] = ['columnSelector-false', 'custom'];
        }

        return $columns;
    }

    public function run()
    {
        $account = Auth::user()->account;
        $statusIds = $this->options['status_ids'];
        $exportFormat = $this->options['export_format'];
        $subgroup = $this->options['subgroup'];
        $hasTaxRates = TaxRate::scope()->count();

        $vendors = Vendor::scope()
            ->orderBy('name')
            ->withArchived()
            ->with('contacts', 'user')
            ->with(['bills' => function ($query) use ($statusIds) {
                $query->bills()
                    ->withArchived()
                    ->statusIds($statusIds)
                    ->where('bill_date', '>=', $this->startDate)
                    ->where('bill_date', '<=', $this->endDate)
                    ->with(['payments' => function ($query) {
                        $query->withArchived()
                            ->excludeFailed()
                            ->with('payment_type', 'account_gateway.gateway');
                    }, 'invoice_items', 'invoice_status']);
            }]);


        if ($this->isExport && $exportFormat == 'zip') {
            if (!extension_loaded('GMP')) {
                die(trans('texts.gmp_required'));
            }

            $zip = Archive::instance_by_useragent(date('Y-m-d') . '_' . str_replace(' ', '_', trans('texts.invoice_documents')));
            foreach ($vendors->get() as $vendor) {
                foreach ($vendor->bills as $bill) {
                    foreach ($bill->documents as $document) {
                        $name = sprintf('%s_%s_%s', $bill->bill_date ?: date('Y-m-d'), $bill->present()->titledName, $document->name);
                        $zip->add_file($name, $document->getRaw());
                    }
                }
            }
            $zip->finish();
            exit;
        }

        if ($this->isExport && $exportFormat == 'zip-bills') {
            if (!extension_loaded('GMP')) {
                die(trans('texts.gmp_required'));
            }
            $zip = Archive::instance_by_useragent(date('Y-m-d') . '_' . str_replace(' ', '_', trans('texts.bills')));
            foreach ($vendors->get() as $vendor) {
                foreach ($vendor->bills as $bill) {
                    $zip->add_file($bill->getFileName(), $bill->getPDFString());
                }
            }
            $zip->finish();
            exit;
        }

        foreach ($vendors->get() as $vendor) {
            foreach ($vendor->bills as $bill) {
                $isFirst = true;
                $payments = $bill->payments->count() ? $bill->payments : [false];
                foreach ($payments as $payment) {
                    $row = [
                        $this->isExport ? $vendor->getDisplayName() : $vendor->present()->link,
                        $this->isExport ? $bill->invoice_number : $bill->present()->link,
                        $this->isExport ? $bill->bill_date : $bill->present()->bill_date,
                        $isFirst ? $account->formatMoney($bill->amount, $vendor) : '',
                        $bill->statusLabel(),
                        $payment ? ($this->isExport ? $payment->payment_date : $payment->present()->payment_date) : '',
                        $payment ? $account->formatMoney($payment->getCompletedAmount(), $vendor) : '',
                        $payment ? $payment->present()->method : '',
                        $this->isExport ? $bill->due_date : $bill->present()->due_date,
                        $bill->po_number,
                        $bill->private_notes,
                        $vendor->vat_number,
                        $bill->user->getDisplayName(),
                    ];

                    if ($hasTaxRates) {
                        $row[] = $isFirst ? $account->formatMoney($bill->getTaxTotal(), $vendor) : '';
                    }

                    if ($account->customLabel('invoice_text1')) {
                        $row[] = $bill->custom_text_value1;
                    }
                    if ($account->customLabel('invoice_text2')) {
                        $row[] = $bill->custom_text_value2;
                    }

                    $this->data[] = $row;

                    $this->addToTotals($vendor->currency_id, 'paid', $payment ? $payment->getCompletedAmount() : 0);
                    $isFirst = false;
                }

                $this->addToTotals($vendor->currency_id, 'amount', $bill->amount);
                $this->addToTotals($vendor->currency_id, 'balance', $bill->balance);

                if ($subgroup == 'status') {
                    $dimension = $bill->statusLabel();
                } else {
                    $dimension = $this->getDimension($vendor);
                }

                $this->addChartData($dimension, $bill->bill_date, $bill->amount);
            }
        }
    }
}
