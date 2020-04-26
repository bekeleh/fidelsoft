<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ApprovalStatusDatatable extends EntityDatatable
{
    public $entityType = ENTITY_APPROVAL_STATUS;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'approval_status_name',
                function ($model) {
                    $str = link_to("approval_statuses/{$model->public_id}", $model->approval_status_name ?: '')->toHtml();
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
                trans('texts.edit_approval_status'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_APPROVAL_STATUS, $model]))
                        return URL::to("approval_statuses/{$model->public_id}/edit");
                    elseif (Auth::user()->can('view', [ENTITY_APPROVAL_STATUS, $model]))
                        return URL::to("approval_statuses/{$model->public_id}");
                },
            ],
            [
                trans('texts.clone_approval_status'),
                function ($model) {
                    return URL::to("approval_statuses/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_APPROVAL_STATUS);
                },
            ],
        ];
    }
}
