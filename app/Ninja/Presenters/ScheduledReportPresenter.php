<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ScheduledReportPresenter extends EntityPresenter
{
    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function skypeBot($account)
    {
        $ScheduledReport = $this->entity;

        $card = new HeroCard();
        $card->setTitle($ScheduledReport->name);
        $card->setSubitle($account->formatMoney($ScheduledReport->cost));
        $card->setText($ScheduledReport->notes);

        return $card;
    }

    public function moreActions()
    {
        $ScheduledReport = $this->entity;
        $actions = [];

        if (!$ScheduledReport->trashed()) {
            if (auth()->user()->can('create', ENTITY_SCHEDULED_REPORT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_scheduled_report')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_scheduled_report")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_scheduled_report")];
        }
        if (!$ScheduledReport->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_scheduled_report")];
        }

        return $actions;
    }

}
