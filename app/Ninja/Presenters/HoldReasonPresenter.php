<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class HoldReasonPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $holdReason = $this->entity;

        $card = new HeroCard();
        $card->setTitle($holdReason->name);
        $card->setText($holdReason->notes);

        return $card;
    }

    public function moreActions()
    {
        $holdReason = $this->entity;
        $actions = [];

        if (!$holdReason->trashed()) {
            if (auth()->user()->can('create', ENTITY_HOLD_REASON)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_hold_reason')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_hold_reason")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_hold_reason")];
        }
        if (!$holdReason->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_hold_reason")];
        }

        return $actions;
    }

}
