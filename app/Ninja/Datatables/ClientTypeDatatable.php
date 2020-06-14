<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ClientTypeDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT_TYPE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'client_type_name',
                function ($model) {
                    $str = link_to("client_types/{$model->public_id}", $model->client_type_name ?: '')->toHtml();
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
                trans('texts.edit_client_type'),
                function ($model) {
                    return URL::to("client_types/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_CLIENT_TYPE);
                },
            ],
            [
                trans('texts.clone_client_type'),
                function ($model) {
                    return URL::to("client_types/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_CLIENT_TYPE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_CLIENT_TYPE]);
                },
            ],
        ];
    }
}
