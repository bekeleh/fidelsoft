<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\HeroCard;
use DropdownButton;

class InvoiceItemPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $invoiceItem = $this->entity;

        $card = new HeroCard();
        $card->setTitle($invoiceItem->name);
        $card->setText($invoiceItem->notes);

        return $card;
    }

    public function moreActions()
    {
        $invoiceItem = $this->entity;
        $actions = [];

        if (!$invoiceItem->trashed()) {
            if (auth()->user()->can('create', ENTITY_INVOICE_ITEM)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_invoice_item')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_invoice_item")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_invoice_item")];
        }
        if (!$invoiceItem->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_invoice_item")];
        }

        return $actions;
    }

}
