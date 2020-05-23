<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ScheduleCategoryPresenter extends EntityPresenter
{
    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function skypeBot($account)
    {
        $scheduleCategory = $this->entity;

        $card = new HeroCard();
        $card->setTitle($scheduleCategory->name);
        $card->setSubitle($account->formatMoney($scheduleCategory->cost));
        $card->setText($scheduleCategory->notes);

        return $card;
    }

    public function moreActions()
    {
        $scheduleCategory = $this->entity;
        $actions = [];

        if (!$scheduleCategory->trashed()) {
            if (auth()->user()->can('create', ENTITY_SCHEDULE_CATEGORY)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_schedule_category')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_schedule_category")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_schedule_category")];
        }
        if (!$scheduleCategory->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_schedule_category")];
        }

        return $actions;
    }

}
