<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ManufacturerDatatable extends EntityDatatable
{
    public $entityType = ENTITY_MANUFACTURER;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'manufacturer_name',
                function ($model) {
                    return link_to('manufacturers/' . $model->public_id . '/edit', $model->manufacturer_name)->toHtml();
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
                mtrans('manufacturer', 'edit_manufacturer'),
                function ($model) {
                    return URL::to("manufacturers/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_MANUFACTURER, $model->user_id]);
                }
            ],
            [
                uctrans('texts.clone_manufacturer'),
                function ($model) {
                    return URL::to("manufacturers/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_MANUFACTURER);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_MANUFACTURER]);
                },
            ],
        ];
    }
}
