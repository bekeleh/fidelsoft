<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemRequestPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemRequest = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemRequest->name);
        $card->setText($itemRequest->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemRequest = $this->entity;
        $actions = [];

        if (!$itemRequest->trashed()) {
//            if (auth()->user()->can('create', ENTITY_ITEM_REQUEST)) {
//                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_request')];
//            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_request")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_request")];
        }
        if (!$itemRequest->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_request")];
        }

        return $actions;
    }

}
