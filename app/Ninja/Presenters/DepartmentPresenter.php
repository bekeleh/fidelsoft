<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class DepartmentPresenter extends EntityPresenter
{
    public function name()
    {
        return $this->entity->getUpperAttributes();
    }

    public function skypeBot($account)
    {
        $department = $this->entity;

        $card = new HeroCard();
        $card->setTitle($department->name);
        $card->setText($department->notes);

        return $card;
    }

    public function moreActions()
    {
        $department = $this->entity;
        $actions = [];

        if (!$department->trashed()) {
            if (auth()->user()->can('create', ENTITY_DEPARTMENT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_department')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_department")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("relocation")', 'label' => trans("texts.restore_department")];
        }
        if (!$department->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_department")];
        }

        return $actions;
    }

}
