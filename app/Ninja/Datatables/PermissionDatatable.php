<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class PermissionDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PERMISSION;
    public $sortCol = 1;

    public function columns()
    {
        return [
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
                trans('texts.edit_permission'),
                function ($model) {
                    return URL::to("permissions/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PERMISSION);
                },
            ],
            [
                trans('texts.clone_permission'),
                function ($model) {
                    return URL::to("permissions/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PERMISSION);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PERMISSION]);
                },
            ],
        ];
    }
}
