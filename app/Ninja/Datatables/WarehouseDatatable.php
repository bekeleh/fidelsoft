<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class WarehouseDatatable extends EntityDatatable
{
    public $entityType = ENTITY_WAREHOUSE;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'warehouse_name',
                function ($model) {
                    return link_to('warehouses/' . $model->public_id . '/edit', $model->warehouse_name)->toHtml();
                },
            ],
            [
                'location_name',
                function ($model) {
                    if ($model->location_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_LOCATION, $model]))
                            return link_to("locations/{$model->location_public_id}", $model->location_name)->toHtml();
                        else
                            return $model->location_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
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
                uctrans('texts.edit_warehouse'),
                function ($model) {
                    return URL::to("warehouses/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_WAREHOUSE);
                },
            ],
            [
                trans('texts.clone_warehouse'),
                function ($model) {
                    return URL::to("warehouses/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_WAREHOUSE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_WAREHOUSE]);
                },
            ],
        ];
    }
}
