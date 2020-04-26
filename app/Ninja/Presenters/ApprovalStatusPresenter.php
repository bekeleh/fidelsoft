<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class StatusPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $Status = $this->entity;

        $card = new HeroCard();
        $card->setTitle($Status->name);
        $card->setText($Status->notes);

        return $card;
    }

    public function moreActions()
    {
        $Status = $this->entity;
        $actions = [];

        if (!$Status->trashed()) {
            if (auth()->user()->can('create', ENTITY_APPROVAL_STATUS)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_approval_status')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_approval_status")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_approval_status")];
        }
        if (!$Status->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_approval_status")];
        }

        return $actions;
    }

}
