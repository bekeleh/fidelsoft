<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class PermissionGroupPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $permissionGroup = $this->entity;

        $card = new HeroCard();
        $card->setTitle($permissionGroup->name);
        $card->setText($permissionGroup->notes);

        return $card;
    }

    public function moreActions()
    {
        $permissionGroup = $this->entity;
        $actions = [];

        if (!$permissionGroup->trashed()) {
            if (auth()->user()->can('create', ENTITY_PERMISSION_GROUP)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_permission_group')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_permission_group")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_permission_group")];
        }

        if (!$permissionGroup->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_permission_group")];
        }

        return $actions;
    }

}
