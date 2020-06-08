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
                'item_name',
                function ($model) {
                    return link_to('products/' . $model->public_id . '/edit', $model->item_name)->toHtml();
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    return link_to('item_brands/' . $model->public_id . '/edit', $model->item_brand_name)->toHtml();
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    return link_to('item_categories/' . $model->public_id . '/edit', $model->item_category_name)->toHtml();
                },
            ],
//            [
//                'item_serial',
//                function ($model) {
//                    return $model->item_serial;
//                },
//            ],
//            [
//                'item_barcode',
//                function ($model) {
//                    return $model->item_barcode;
//                },
//            ],
//            [
//                'item_tag',
//                function ($model) {
//                    return $model->item_tag;
//                },
//            ],
            [
                'cost',
                function ($model) {
                    return $model->cost;
                },
            ],
            [
                'unit_name',
                function ($model) {
                    return link_to('units/' . $model->public_id . '/edit', $model->unit_name)->toHtml();
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
//                $account->invoice_item_taxes,
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
                uctrans('texts.detail_product'),
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
            [
                trans('texts.invoice_product'),
                function ($model) {
                    return "javascript:submitForm_product('invoice', {$model->public_id})";
                },
                function ($model) {
                    return (!$model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
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
