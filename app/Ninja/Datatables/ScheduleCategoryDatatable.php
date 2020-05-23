<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ScheduleCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SCHEDULE_CATEGORY;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'schedule_category_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_SCHEDULE_CATEGORY, $model]))
                        return link_to("schedule_categories/{$model->public_id}/edit", $model->schedule_category_name)->toHtml();
                    else
                        return $model->schedule_category_name;

                },
            ],
            [
                'text_color',
                function ($model) {
                    return $model->text_color;
                },
            ],
            [
                'bg_color',
                function ($model) {
                    return $model->bg_color;
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
                trans('texts.edit_category'),
                function ($model) {
                    return URL::to("schedule_categories/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE_CATEGORY, $model]);
                },
            ],
            [
                trans('texts.clone_schedule_category'),
                function ($model) {
                    return URL::to("schedule_categories/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_SCHEDULE_CATEGORY, $model]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE_CATEGORY]);
                },
            ],
        ];
    }
}
