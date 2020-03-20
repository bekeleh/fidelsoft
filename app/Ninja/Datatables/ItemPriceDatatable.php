<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemPriceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_PRICE;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'item_name',
                function ($model) {
                    return link_to('products/' . $model->public_id . '/edit', $model->item_name)->toHtml();
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    return link_to('item_categories/' . $model->public_id . '/edit', $model->item_category_name)->toHtml();
                },
            ],
            [
                'sale_type_name',
                function ($model) {
                    if ($model->sale_type_id) {
                        if (Auth::user()->can('view', [ENTITY_SALE_TYPE, $model]))
                            return link_to("sale_types/{$model->sale_type_id}", $model->sale_type_name)->toHtml();
                        else
                            return $model->sale_type_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'item_price',
                function ($model) {
                    return self::getStatusLabel($model);
                },
            ],
            [
                'item_cost',
                function ($model) {
                    return $model->item_cost;
                },
            ],
            [
                'start_date',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->start_date));
                },
            ],
            [
                'end_date',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->end_date));
                },
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
                },
            ],
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
        ];
    }

    public function actions()
    {
        return [
            [
                uctrans('texts.edit_item_price'),
                function ($model) {
                    return URL::to("item_prices/{$model->public_id}/edit");
                },
            ],
            [
                trans('texts.clone_item_price'),
                function ($model) {
                    return URL::to("item_prices/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_PRICE);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {

        $class = ItemPrice::getStatusClass($model->item_price, $model->item_cost);

        return "<h4><div class=\"label label-{$class}\">$model->item_price</div></h4>";
    }
}
