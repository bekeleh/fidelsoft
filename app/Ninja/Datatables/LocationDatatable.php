<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class LocationDatatable extends EntityDatatable
{
    public $entityType = ENTITY_LOCATION;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'location_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_LOCATION]))
                        return link_to("locations/{$model->public_id}", $model->location_name ?: '')->toHtml();
                    else
                        return $model->location_name;
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
                uctrans('texts.edit_location'),
                function ($model) {
                    return URL::to("locations/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_LOCATION);
                },
            ],
            [
                trans('texts.clone_location'),
                function ($model) {
                    return URL::to("locations/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_LOCATION);
                },
            ],
        ];
    }
}
