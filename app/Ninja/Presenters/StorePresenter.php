<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class StorePresenter extends EntityPresenter
{
    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function name()
    {
        return $this->entity->getUpperAttributes();
    }

    public function location()
    {
        return $this->entity->location ? $this->entity->location->getDisplayName() : '';
    }

    public function skypeBot($account)
    {
        $store = $this->entity;

        $card = new HeroCard();
        $card->setTitle($store->name);
        $card->setText($store->notes);

        return $card;
    }

    public function moreActions()
    {
        $store = $this->entity;
        $actions = [];

        if (!$store->trashed()) {
            if (auth()->user()->can('create', ENTITY_PRODUCT)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_store')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_store")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_store")];
        }
        if (!$store->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_store")];
        }

        return $actions;
    }

}
