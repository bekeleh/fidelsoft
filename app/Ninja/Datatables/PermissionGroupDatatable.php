<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class PermissionGroupDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PERMISSION_GROUP;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'permission_group_name',
                function ($model) {
                    $str = link_to("permission_groups/{$model->public_id}", $model->permission_group_name ?: '')->toHtml();
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
                trans('texts.edit_group'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_PERMISSION_GROUP, $model]))
                        return URL::to("permission_groups/{$model->public_id}/edit");
                    elseif (Auth::user()->can('view', [ENTITY_PERMISSION_GROUP, $model]))
                        return URL::to("permission_groups/{$model->public_id}");
                },
            ],
            [
                trans('texts.clone_group'),
                function ($model) {
                    return URL::to("permission_groups/{$model->public_id}/clone");
                }
            ],
        ];
    }
}