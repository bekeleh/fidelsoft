<?php

namespace App\Libraries\Skype;

class InvoiceItemCard
{
    public function __construct($invoiceItem, $account)
    {
        $this->title = intval($invoiceItem->qty) . ' ' . $invoiceItem->name;
        $this->subtitle = $invoiceItem->notes;
        $this->quantity = $invoiceItem->qty;
        $this->price = $account->formatMoney($invoiceItem->cost);
    }
}
