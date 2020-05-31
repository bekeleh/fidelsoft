<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class TaxRatePresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $taxRate = $this->entity;

        $card = new HeroCard();
        $card->setTitle($taxRate->name);
        $card->setText($taxRate->notes);

        return $card;
    }

    public function moreActions()
    {
        $taxRate = $this->entity;
        $actions = [];

        if (!$taxRate->trashed()) {
            if (auth()->user()->can('create', ENTITY_TAX_RATE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_tax_rate')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_tax_rate")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_tax_rate")];
        }
        if (!$taxRate->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_tax_rate")];
        }

        return $actions;
    }

}
