<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemMovementDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_MOVEMENT;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_key',
                function ($model) {
                    if ($model->product_public_id) {
                        return link_to('products/' . $model->product_public_id . '/edit', $model->product_key)->toHtml();
                    } else {
                        $model->product_key;
                    }
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    if ($model->item_brand_public_id) {
                        return link_to('item_brands/' . $model->item_brand_public_id . '/edit', $model->item_brand_name)->toHtml();
                    } else {
                        $model->item_brand_name;
                    }
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    if ($model->item_category_public_id) {
                        return link_to('item_categories/' . $model->item_category_public_id . '/edit', $model->item_category_name)->toHtml();
                    } else {
                        $model->item_category_name;
                    }
                },
            ],
            [
                'store_name',
                function ($model) {
                    if ($model->store_name_public_id) {
                        return link_to('item_movements/' . $model->public_id . '/edit', $model->store_name)->toHtml();
                    } else {
                        $model->store_name;
                    }
                }
            ],
            [
                'qty',
                function ($model) {
                    return $model->qty;
                },
            ],
            [
                'qoh',
                function ($model) {
                    return $model->qoh;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
                },
            ],
            [
                'created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'updated_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->updated_at));
                },
            ],
//            [
//                'date_deleted',
//                function ($model) {
//                    return Utils::timestampToDateString(strtotime($model->deleted_at));
//                },
//            ],
            [
                'created_by',
                function ($model) {
                    return $model->created_by;
                },
            ],
            [
                'updated_by',
                function ($model) {
                    return $model->updated_by;
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            // [
            //     uctrans('texts.edit_item_movement'),
            //     function ($model) {
            //         return URL::to("item_movements/{$model->public_id}/edit");
            //     },
            //     function ($model) {
            //         return Auth::user()->can('edit', ENTITY_ITEM_MOVEMENT);
            //     },
            // ],
            // [
            //     trans('texts.clone_item_movement'),
            //     function ($model) {
            //         return URL::to("item_movements/{$model->public_id}/clone");
            //     },
            //     function ($model) {
            //         return Auth::user()->can('create', ENTITY_ITEM_MOVEMENT);
            //     },
            // ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_MOVEMENT]);
                },
            ],
        ];
    }
}
