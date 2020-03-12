<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemStorePresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemStore = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemStore->name);
        $card->setText($itemStore->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemStore = $this->entity;
        $actions = [];

        if (!$itemStore->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_STORE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_store')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_store")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_store")];
        }
        if (!$itemStore->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_store")];
        }

        return $actions;
    }

}
