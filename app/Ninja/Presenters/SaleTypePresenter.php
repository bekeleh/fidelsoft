<?php

namespace App\Ninja\Presenters;

use DropdownButton;
use App\Libraries\Skype\HeroCard;

class SaleTypePresenter extends EntityPresenter
{

    public function skypeBot($account)
    {
        $saleType = $this->entity;

        $card = new HeroCard();
        $card->setTitle($saleType->name);
        $card->setText($saleType->notes);

        return $card;
    }

    public function moreActions()
    {
        $saleType = $this->entity;
        $actions = [];

        if (!$saleType->trashed()) {
            if (auth()->user()->can('create', ENTITY_SALE_TYPE)) {
                $actions[] = ['url' => 'javascript:submitAction("clone")', 'label' => trans('texts.clone_sale_type')];
            }
            if (count($actions)) {
                $actions[] = DropdownButton::DIVIDER;
            }
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans("texts.archive_sale_type")];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans("texts.restore_sale_type")];
        }
        if (!$saleType->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_sale_type")];
        }

        return $actions;
    }

}
