<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class UnitPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $unit = $this->entity;

        $card = new HeroCard();
        $card->setTitle($unit->name);
        $card->setText($unit->notes);

        return $card;
    }

    public function moreActions()
    {
        $unit = $this->entity;
        $actions = [];

        if (!$unit->trashed()) {
            if (auth()->user()->can('create', ENTITY_UNIT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_unit')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_unit")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_unit")];
        }
        if (!$unit->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_unit")];
        }

        return $actions;
    }

}
