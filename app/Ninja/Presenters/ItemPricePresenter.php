<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemPricePresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemPrice = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemPrice->name);
        $card->setText($itemPrice->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemPrice = $this->entity;
        $actions = [];

        if (!$itemPrice->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_PRICE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_price')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_price")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_price")];
        }
        if (!$itemPrice->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_price")];
        }

        return $actions;
    }

}
