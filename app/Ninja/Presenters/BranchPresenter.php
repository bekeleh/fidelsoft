<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class BranchPresenter extends EntityPresenter
{
    public function name()
    {
        return $this->entity->getUpperAttributes();
    }

    public function skypeBot($account)
    {
        $branch = $this->entity;

        $card = new HeroCard();
        $card->setTitle($branch->name);
        $card->setText($branch->notes);

        return $card;
    }

    public function moreActions()
    {
        $branch = $this->entity;
        $actions = [];

        if (!$branch->trashed()) {
            if (auth()->user()->can('create', ENTITY_BRANCH)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_branch')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_branch")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("relocation")', 'label' => trans("texts.restore_branch")];
        }
        if (!$branch->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_branch")];
        }

        return $actions;
    }

}
