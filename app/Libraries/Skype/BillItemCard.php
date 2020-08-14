<?php

namespace App\Libraries\Skype;

class BillItemCard
{
    public function __construct($billItem, $account)
    {
        $this->title = intval($billItem->qty) . ' ' . $billItem->name;
        $this->subtitle = $billItem->notes;
        $this->quantity = $billItem->qty;
        $this->price = $account->formatMoney($billItem->cost);
    }
}
