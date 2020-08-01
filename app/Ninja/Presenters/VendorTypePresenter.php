<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class VendorTypePresenter extends EntityPresenter
{

    public function skypeBot($account)
    {
        $vendorType = $this->entity;

        $card = new HeroCard();
        $card->setTitle($vendorType->name);
        $card->setText($vendorType->notes);

        return $card;
    }

    public function moreActions()
    {
        $vendorType = $this->entity;
        $actions = [];

        if (!$vendorType->trashed()) {
            if (auth()->user()->can('create', ENTITY_VENDOR_TYPE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_vendor_type')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_vendor_type")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_vendor_type")];
        }
        if (!$vendorType->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_vendor_type")];
        }

        return $actions;
    }

}
