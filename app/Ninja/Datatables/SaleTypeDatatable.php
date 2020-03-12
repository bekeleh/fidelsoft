<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class SaleTypeDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SALE_TYPE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'name',
                function ($model) {
                    $str = link_to("sale_types/{$model->public_id}", $model->name ?: '')->toHtml();
                    return $str;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $model->notes;
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
                trans('texts.edit_sale_type'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_SALE_TYPE, $model]))
                        return URL::to("sale_types/{$model->public_id}/edit");
                    elseif (Auth::user()->can('view', [ENTITY_SALE_TYPE, $model]))
                        return URL::to("sale_types/{$model->public_id}");
                },
            ],
            [
                trans('texts.clone_sale_type'),
                function ($model) {
                    return URL::to("sale_types/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_SALE_TYPE);
                },
            ],
        ];
    }
}
