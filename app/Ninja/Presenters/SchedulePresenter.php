<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class SchedulePresenter extends EntityPresenter
{
    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function skypeBot($account)
    {
        $Schedule = $this->entity;

        $card = new HeroCard();
        $card->setTitle($Schedule->name);
        $card->setSubitle($account->formatMoney($Schedule->cost));
        $card->setText($Schedule->notes);

        return $card;
    }

    public function moreActions()
    {
        $Schedule = $this->entity;
        $actions = [];

        if (!$Schedule->trashed()) {
            if (auth()->user()->can('create', ENTITY_SCHEDULE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_schedule')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_schedule")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_schedule")];
        }
        if (!$Schedule->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_schedule")];
        }

        return $actions;
    }

}
