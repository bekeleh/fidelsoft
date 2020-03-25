<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\HeroCard;
use DropdownButton;

class UserPresenter extends EntityPresenter
{
    public function email()
    {
        return htmlentities(sprintf('%s <%s>', $this->fullName(), $this->entity->email));
    }

    public function fullName()
    {
        return $this->entity->first_name . ' ' . $this->entity->last_name;
    }

    public function location()
    {
        return $this->entity->location ? $this->entity->location->getDisplayName() : '';
    }

    public function skypeBot($account)
    {
        $user = $this->entity;

        $card = new HeroCard();
        $card->setTitle($user->name);
        $card->setText($user->notes);

        return $card;
    }

    public function moreActions()
    {
        $user = $this->entity;
        $actions = [];

        if (!$user->trashed()) {
            if (auth()->user()->can('create', ENTITY_USER)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_user')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_user")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_user")];
        }
        if (!$user->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_user")];
        }

        return $actions;
    }

    public function statusCode()
    {
        $status = '';
        $user = $this->entity;
        $account = $user->account;

        if ($user->confirmed) {
            $status .= 'C';
        } elseif ($user->registered) {
            $status .= 'R';
        } else {
            $status .= 'N';
        }

        if ($account->isTrial()) {
            $status .= 'T';
        } elseif ($account->isEnterprise()) {
            $status .= 'E';
        } elseif ($account->isPro()) {
            $status .= 'P';
        } else {
            $status .= 'H';
        }

        return $status;
    }
}
