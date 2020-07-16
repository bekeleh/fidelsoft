<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class WarehousePresenter extends EntityPresenter
{
    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function name()
    {
        return $this->entity->getUpperAttributes();
    }

    public function skypeBot($account)
    {
        $warehouse = $this->entity;

        $card = new HeroCard();
        $card->setTitle($warehouse->name);
        $card->setText($warehouse->notes);

        return $card;
    }

    public function moreActions()
    {
        $warehouse = $this->entity;
        $actions = [];

        if (!$warehouse->trashed()) {
            if (auth()->user()->can('create', ENTITY_WAREHOUSE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_warehouse')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_warehouse")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_warehouse")];
        }
        if (!$warehouse->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_warehouse")];
        }

        return $actions;
    }

}
