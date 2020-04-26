<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ApprovalStatusPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $approvalStatus = $this->entity;

        $card = new HeroCard();
        $card->setTitle($approvalStatus->name);
        $card->setText($approvalStatus->notes);

        return $card;
    }

    public function moreActions()
    {
        $approvalStatus = $this->entity;
        $actions = [];

        if (!$approvalStatus->trashed()) {
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
        if (!$approvalStatus->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_approval_status")];
        }

        return $actions;
    }

}