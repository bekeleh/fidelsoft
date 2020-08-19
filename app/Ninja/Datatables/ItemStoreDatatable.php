<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemStoreDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_STORE;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_key',
                function ($model) {
                    if ($model->product_public_id) {
                        return link_to("products/{$model->product_public_id}",
                            $model->product_key)->toHtml();
                    } else {
                        return $model->product_key;
                    }
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    if ($model->item_brand_public_id) {
                        return link_to("item_brands/{$model->item_brand_public_id}",
                            $model->item_brand_name)->toHtml();
                    } else {
                        return $model->item_brand_name;
                    }
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    if ($model->item_category_public_id) {
                        return link_to("item_categories/{$model->item_category_public_id}",
                            $model->item_category_name)->toHtml();
                    } else {
                        return $model->item_category_name;
                    }
                },
            ],
            [
                'warehouse_name',
                function ($model) {
                    if ($model->store_public_id) {
                        return link_to("warehouses/{$model->store_public_id}",
                            $model->store_name)->toHtml();
                    } else {
                        return $model->store_name;
                    }
                },
            ],
            [
                'bin',
                function ($model) {
                    return $model->bin;
                },
            ],
            [
                'qty',
                function ($model) {
                    return self::getStatusLabel($model);
                },
            ],
            [
                'reorder_level',
                function ($model) {
                    return $model->reorder_level;
                },
            ],
            [
                'EOQ',
                function ($model) {
                    return $model->EOQ;
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
                uctrans('texts.edit_item_store'),
                function ($model) {
                    return URL::to("item_stores/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_ITEM_STORE);
                },
            ],
            [
                trans('texts.clone_item_store'),
                function ($model) {
                    return URL::to("item_stores/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_STORE);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemStore::calcStatusClass($model->qty, $model->reorder_level);

        return "<h4><div class=\"label label-{$class}\">$model->qty</div></h4>";
    }
}
