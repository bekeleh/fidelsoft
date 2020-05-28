<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class SubscriptionDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SUBSCRIPTION;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'event',
                function ($model) {
                    return trans('texts.subscription_event_' . $model->event);
                },
            ],
            [
                'target',
                function ($model) {
                    return $this->showWithTooltip($model->target, 40);
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
                uctrans('texts.edit_subscription'),
                function ($model) {
                    return URL::to("subscriptions/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SUBSCRIPTION]);
                },
            ],
            [
                uctrans('texts.clone_subscription'),
                function ($model) {
                    return URL::to("subscriptions/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_SUBSCRIPTION]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_SUBSCRIPTION]);
                },
            ],
        ];
    }
}
