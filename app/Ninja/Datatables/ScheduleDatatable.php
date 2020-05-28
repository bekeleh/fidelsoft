<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ScheduleDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SCHEDULE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'title',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_SCHEDULE, $model]))
                        return link_to("schedules/{$model->public_id}/edit", $model->title)->toHtml();
                    else
                        return $model->title;

                },
            ],
            [
                'description',
                function ($model) {
                    return $model->description;
                },
            ],
            [
                'notes',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->notes));
                },
            ],
            [
                'rrule',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->rrule));
                },
            ],
            [
                'url',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->url));
                },
            ],
            [
                'will_call',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->will_call));
                },
            ],
            [
                'isRecurring',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->isRecurring));
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
                trans('texts.edit_schedule'),
                function ($model) {
                    return URL::to("schedules/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE, $model]);
                },
            ],
            [
                trans('texts.clone_schedule'),
                function ($model) {
                    return URL::to("schedules/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_SCHEDULE, $model]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE]);
                },
            ],
        ];
    }
}
