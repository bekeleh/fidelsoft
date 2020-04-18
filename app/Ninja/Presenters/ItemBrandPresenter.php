<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemBrandPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemBrand = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemBrand->name);
        $card->setText($itemBrand->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemBrand = $this->entity;
        $actions = [];

        if (!$itemBrand->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_BRAND)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_brand')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_brand")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_brand")];
        }
        if (!$itemBrand->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_brand")];
        }

        return $actions;
    }

}
