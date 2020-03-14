<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemMovementPresenter extends EntityPresenter
{

    public function skypeBot($account)
    {
        $itemMovement = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemMovement->name);
        $card->setText($itemMovement->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemMovement = $this->entity;
        $actions = [];

        if (!$itemMovement->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_MOVEMENT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_movement')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_movement")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_movement")];
        }
        if (!$itemMovement->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_movement")];
        }

        return $actions;
    }

}
