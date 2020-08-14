<?php

namespace App\Libraries\Skype;

use HTML;
use stdClass;

class BillCard
{
    public function __construct($bill)
    {
        $this->contentType = 'application/vnd.microsoft.card.receipt';
        $this->content = new stdClass();
        $this->content->facts = [];
        $this->content->items = [];
        $this->content->buttons = [];

        $this->setTitle('test');

        $this->setTitle(trans('texts.bill_for_vendor', [
            'bill' => link_to($bill->getRoute(), $bill->bill_number),
            'vendor' => link_to($bill->vendor->getRoute(), $bill->vendor->getDisplayName()),
        ]));

        $this->addFact(trans('texts.email'), HTML::mailto($bill->vendor->contacts[0]->email)->toHtml());

        if ($bill->due_date) {
            $this->addFact($bill->present()->dueDateLabel, $bill->present()->due_date);
        }

        if ($bill->po_number) {
            $this->addFact(trans('texts.po_number'), $bill->po_number);
        }

        if ($bill->discount) {
            $this->addFact(trans('texts.discount'), $bill->present()->discount);
        }

        foreach ($bill->bill_items as $item) {
            $this->addItem($item, $bill->account);
        }

        $this->setTotal($bill->present()->requestedAmount);

        if (floatval($bill->amount)) {
            $this->addButton(SKYPE_BUTTON_OPEN_URL, trans('texts.download_pdf'), $bill->getInvitationLink('download', true));
            $this->addButton(SKYPE_BUTTON_IM_BACK, trans('texts.email_bill'), trans('texts.email_bill'));
        } else {
            $this->addButton(SKYPE_BUTTON_IM_BACK, trans('texts.list_products'), trans('texts.list_products'));
        }
    }

    public function setTitle($title)
    {
        $this->content->title = $title;
    }

    public function setTotal($value)
    {
        $this->content->total = $value;
    }

    public function addFact($key, $value)
    {
        $fact = new stdClass();
        $fact->key = $key;
        $fact->value = $value;

        $this->content->facts[] = $fact;
    }

    public function addItem($item, $account)
    {
        $this->content->items[] = new BillItemCard($item, $account);
    }

    public function addButton($type, $title, $value, $url = false)
    {
        $this->content->buttons[] = new ButtonCard($type, $title, $value, $url);
    }
}
