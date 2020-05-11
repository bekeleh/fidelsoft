<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class ManufacturerProductDetailsPresenter extends EntityPresenter
{
    public function skypeBot($account)
    {
        $manufacturerProductDetail = $this->entity;

        $card = new HeroCard();
        $card->setTitle($manufacturerProductDetail->name);
        $card->setText($manufacturerProductDetail->notes);

        return $card;
    }

    public function moreActions()
    {
        $manufacturerProductDetail = $this->entity;
        $actions = [];

        if (!$manufacturerProductDetail->trashed()) {
            if (auth()->user()->can('create', ENTITY_MANUFACTURER_PRODUCT_DETAIL)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_manufacturer_product_detail')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_manufacturer_product_detail")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("relocation")', 'label' => trans("texts.restore_manufacturer_product_detail")];
        }
        if (!$manufacturerProductDetail->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_manufacturer_product_detail")];
        }

        return $actions;
    }

}
