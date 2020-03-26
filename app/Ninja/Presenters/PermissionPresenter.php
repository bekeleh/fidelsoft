<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class PermissionPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $permission = $this->entity;

        $card = new HeroCard();
        $card->setTitle($permission->name);
        $card->setText($permission->notes);

        return $card;
    }

    public function moreActions()
    {
        $permission = $this->entity;
        $actions = [];

        if (!$permission->trashed()) {
            if (auth()->user()->can('create', ENTITY_PERMISSION)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_permission')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_permission")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_permission")];
        }
        if (!$permission->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_permission")];
        }

        return $actions;
    }

}
