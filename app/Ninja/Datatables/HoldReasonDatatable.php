<?php

namespace App\Ninja\Datatables;

use App\Models\HoldReason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class HoldReasonDatatable extends EntityDatatable
{
    public $entityType = ENTITY_HOLD_REASON;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'hold_reason_name',
                function ($model) {
                    $str = link_to("hold_reasons/{$model->public_id}", $model->hold_reason_name ?: '')->toHtml();
                    return $str;
                },
            ],
            [
                'allow_invoice',
                function ($model) {
                    return $this::getTrueFalseFormatter($model);
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
                trans('texts.edit_hold_reason'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_HOLD_REASON, $model]))
                        return URL::to("hold_reasons/{$model->public_id}/edit");
                    elseif (Auth::user()->can('view', [ENTITY_HOLD_REASON, $model]))
                        return URL::to("hold_reasons/{$model->public_id}");
                },
            ],
            [
                trans('texts.clone_hold_reason'),
                function ($model) {
                    return URL::to("hold_reasons/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_HOLD_REASON);
                },
            ],
        ];
    }

    private function getTrueFalseFormatter($model)
    {
        $class = HoldReason::trueFalseFormatter($model->allow_invoice);

        return $class;
    }
}
