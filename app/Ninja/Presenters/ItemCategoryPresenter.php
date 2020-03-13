<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ItemCategoryPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $itemCategory = $this->entity;

        $card = new HeroCard();
        $card->setTitle($itemCategory->name);
        $card->setText($itemCategory->notes);

        return $card;
    }

    public function moreActions()
    {
        $itemCategory = $this->entity;
        $actions = [];

        if (!$itemCategory->trashed()) {
            if (auth()->user()->can('create', ENTITY_ITEM_CATEGORY)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_item_category')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_item_category")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_item_category")];
        }
        if (!$itemCategory->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_item_category")];
        }

        return $actions;
    }

}
