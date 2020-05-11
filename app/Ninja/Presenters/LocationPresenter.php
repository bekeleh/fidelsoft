<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class LocationPresenter extends EntityPresenter
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
        $location = $this->entity;

        $card = new HeroCard();
        $card->setTitle($location->name);
        $card->setText($location->notes);

        return $card;
    }

    public function moreActions()
    {
        $location = $this->entity;
        $actions = [];

        if (!$location->trashed()) {
            if (auth()->user()->can('create', ENTITY_PRODUCT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_location')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_location")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("relocation")', 'label' => trans("texts.restore_location")];
        }
        if (!$location->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_location")];
        }

        return $actions;
    }

}
