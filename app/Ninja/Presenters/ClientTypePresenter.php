<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\HeroCard;
use DropdownButton;

class ClientTypePresenter extends EntityPresenter
{

    public function skypeBot($account)
    {
        $clientType = $this->entity;

        $card = new HeroCard();
        $card->setTitle($clientType->name);
        $card->setText($clientType->notes);

        return $card;
    }

    public function moreActions()
    {
        $clientType = $this->entity;
        $actions = [];

        if (!$clientType->trashed()) {
            if (auth()->user()->can('create', ENTITY_CLIENT_TYPE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_client_type')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_client_type")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_client_type")];
        }
        if (!$clientType->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_client_type")];
        }

        return $actions;
    }

}
