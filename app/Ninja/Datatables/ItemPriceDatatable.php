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
                    if (Auth::user()->can('edit', [ENTITY_PRODUCT, $model])) {
                        return link_to('products/' . $model->public_id . '/edit', $model->item_name)->toHtml();
                    } else {
                        $model->item_name;
                    }
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_ITEM_BRAND, $model])) {
                        return link_to('item_brands/' . $model->public_id . '/edit', $model->item_brand_name)->toHtml();
                    } else {
                        $model->item_brand_name;
                    }
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_ITEM_CATEGORY, $model])) {
                        return link_to('item_categories/' . $model->public_id . '/edit', $model->item_category_name)->toHtml();
                    } else {
                        $model->item_category_name;
                    }
                },
            ],
            [
                'client_type_name',
                function ($model) {
                    if ($model->client_type_id) {
                        if (Auth::user()->can('view', [ENTITY_SALE_TYPE, $model]))
                            return link_to("client_types/{$model->client_type_id}", $model->client_type_name)->toHtml();
                        else
                            return $model->client_type_name;
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
                'cost',
                function ($model) {
                    return $model->cost;
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
            [
                uctrans('texts.edit_item_price'),
                function ($model) {
                    return URL::to("item_prices/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_PRICE]);
                },
            ],
            [
                trans('texts.clone_item_price'),
                function ($model) {
                    return URL::to("item_prices/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_ITEM_PRICE]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_PRICE, $model]);
                },

            ],
        ];
    }

    private function getStatusLabel($model)
    {

        $class = ItemPrice::getStatusClass($model->item_price, $model->cost);

        return "<h4><div class=\"label label-{$class}\">$model->item_price</div></h4>";
    }
}
