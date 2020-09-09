<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ProductDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PRODUCT;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_key',
                function ($model) {
                    return link_to('products/' . $model->public_id . '/edit', $model->product_key)->toHtml();
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
                'upc',
                function ($model) {
                    return $model->upc;
                },
            ],
            [
                'item_barcode',
                function ($model) {
                    return $model->item_barcode;
                },
            ],
            [
                'item_serial',
                function ($model) {
                    return $model->item_serial;
                },
            ],
            [
                'item_tag',
                function ($model) {
                    return $model->item_tag;
                },
            ],
            [
                'cost',
                function ($model) {
                    return $model->cost;
                },
            ],
            [
                'item_type_name',
                function ($model) {
                    return $model->item_type_name;
                },
            ],
            [
                'tax_category_name',
                function ($model) {
                    return $model->tax_category_name;
                },
            ],
            [
                'unit_name',
                function ($model) {
                    return $model->unit_name;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
                },
            ],
            [
                'tax_rate1',
                function ($model) {
                    return $model->tax_rate1 ? ($model->tax_name1 . ' ' . $model->tax_rate1 . '%') : '';
                },
                $account->invoice_item_taxes,
            ],
            [
                'tax_rate2',
                function ($model) {
                    return $model->tax_rate2 ? ($model->tax_name2 . ' ' . $model->tax_rate2 . '%') : '';
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
                uctrans('texts.edit_product'),
                function ($model) {
                    return URL::to("products/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_PRODUCT);
                },
            ],
            [
                trans('texts.clone_product'),
                function ($model) {
                    return URL::to("products/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PRODUCT);
                },
            ],
//            [
//                '--divider--', function () {
//                return false;
//            },
//                function ($model) {
//                    return Auth::user()->can('edit', [ENTITY_PRODUCT]);
//                },
//            ],
//            [
//                uctrans('texts.detail_product'),
//                function ($model) {
//                    return URL::to("products/{$model->public_id}/edit");
//                },
//                function ($model) {
//                    return Auth::user()->can('edit', ENTITY_PRODUCT);
//                },
//            ],
//            [
//                trans('texts.invoice_product'),
//                function ($model) {
//                    return "javascript:submitForm_product('invoice', {$model->public_id})";
//                },
//                function ($model) {
//                    return (!$model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_INVOICE);
//                },
//            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PRODUCT]);
                },
            ],
        ];
    }
}
