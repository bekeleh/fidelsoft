<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class UnitDatatable extends EntityDatatable
{
    public $entityType = ENTITY_UNIT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'unit_name',
                function ($model) {
                    $str = link_to("units/{$model->public_id}", $model->unit_name ?: '')->toHtml();
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
                trans('texts.edit_unit'),
                function ($model) {
                    return URL::to("units/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_UNIT, $model]);
                },
            ],
            [
                trans('texts.clone_unit'),
                function ($model) {
                    return URL::to("units/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_UNIT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_UNIT, $model]);
                },
            ],
        ];
    }
}
