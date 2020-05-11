<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ManufacturerPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $manufacturer = $this->entity;

        $card = new HeroCard();
        $card->setTitle($manufacturer->name);
        $card->setText($manufacturer->notes);

        return $card;
    }

    public function moreActions()
    {
        $manufacturer = $this->entity;
        $actions = [];

        if (!$manufacturer->trashed()) {
            if (auth()->user()->can('create', ENTITY_MANUFACTURER)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_manufacturer')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_manufacturer")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("relocation")', 'label' => trans("texts.relocation_manufacturer")];
        }
        if (!$manufacturer->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_manufacturer")];
        }

        return $actions;
    }

}
