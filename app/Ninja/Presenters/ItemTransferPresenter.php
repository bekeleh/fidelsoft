<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemTransferPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemTransfer = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemTransfer->name);
        $card->setText($itemTransfer->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemTransfer = $this->entity;
        $actions = [];

        if (!$itemTransfer->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_TRANSFER)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_transfer')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_transfer")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_transfer")];
        }
        if (!$itemTransfer->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_transfer")];
        }

        return $actions;
    }

}
