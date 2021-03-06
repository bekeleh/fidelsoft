<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ProjectDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROJECT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'project',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_PROJECT, $model])){
                        return link_to("projects/{$model->public_id}", $model->project)->toHtml();
                    }else{
                        return $model->project;
                    }
                },
            ],
            [
                'client_name',
                function ($model) {
                    if ($model->client_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_CLIENT, $model]))
                            return link_to("clients/{$model->client_public_id}", $model->client_name)->toHtml();
                        else
                            return Utils::getClientDisplayName($model);

                    } else {
                        return '';
                    }
                },
            ],
            [
                'private_notes',
                function ($model) {
                    return Utils::fromSqlDate($model->private_notes);
                },
            ],
            [
                'due_date',
                function ($model) {
                    return Utils::fromSqlDate($model->due_date);
                },
            ],
            [
                'budgeted_hours',
                function ($model) {
                    return $model->budgeted_hours ?: '';
                },
            ],
            [
                'task_rate',
                function ($model) {
                    return floatval($model->task_rate) ? Utils::formatMoney($model->task_rate) : '';
                }
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
                trans('texts.edit_project'),
                function ($model) {
                    return URL::to("projects/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PROJECT, $model]);
                },
            ],
            [
                trans('texts.clone_project'),
                function ($model) {
                    return URL::to("projects/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_PROJECT, $model]);
                },
            ],
            [
                trans('texts.invoice_project'),
                function ($model) {
                    return "javascript:submitForm_project('invoice', {$model->public_id})";
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PROJECT]);
                },
            ],
        ];
    }
}
