<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class DepartmentDatatable extends EntityDatatable
{
    public $entityType = ENTITY_DEPARTMENT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'department_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_DEPARTMENT]))
                        return link_to("departments/{$model->public_id}", $model->department_name ?: '')->toHtml();
                    else
                        return $model->department_name;
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
                uctrans('texts.edit_department'),
                function ($model) {
                    return URL::to("departments/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_DEPARTMENT);
                },
            ],
            [
                uctrans('texts.clone_department'),
                function ($model) {
                    return URL::to("departments/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_DEPARTMENT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_DEPARTMENT]);
                },
            ],
        ];
    }
}
