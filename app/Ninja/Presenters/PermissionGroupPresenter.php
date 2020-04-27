<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class PermissionGroupPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $group = $this->entity;

        $card = new HeroCard();
        $card->setTitle($group->name);
        $card->setText($group->notes);

        return $card;
    }

    public function moreActions()
    {
        $group = $this->entity;
        $actions = [];

        if (!$group->trashed()) {
            if (auth()->user()->can('create', ENTITY_PERMISSION_GROUP)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_group')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_group")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_group")];
        }
        if (!$group->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_group")];
        }

        return $actions;
    }

}
