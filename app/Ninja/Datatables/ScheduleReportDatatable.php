<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ScheduleReportDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SCHEDULE_REPORT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'ip',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_SCHEDULE_REPORT, $model]))
                        return link_to("schedule_reports/{$model->public_id}/edit", $model->ip)->toHtml();
                    else
                        return $model->ip;

                },
            ],
            [
                'frequency',
                function ($model) {
                    return $model->frequency;
                },
            ],
            [
                'send_date',
                function ($model) {
                    return $model->send_date;
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
                trans('texts.edit_schedule_report'),
                function ($model) {
                    return URL::to("schedule_reports/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE_REPORT, $model]);
                },
            ],
            [
                trans('texts.clone_schedule_report'),
                function ($model) {
                    return URL::to("schedule_reports/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_SCHEDULE_REPORT, $model]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SCHEDULE_REPORT]);
                },
            ],
        ];
    }
}
