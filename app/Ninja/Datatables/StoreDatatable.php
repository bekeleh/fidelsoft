<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class StoreDatatable extends EntityDatatable
{
    public $entityType = ENTITY_STORE;
    public $sortCol = 4;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'name',
                function ($model) {
                    return link_to('stores/' . $model->public_id . '/edit', $model->name)->toHtml();
                },
            ],
            [
                'store_code',
                function ($model) {
                    return $model->store_code;
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
                uctrans('texts.edit_store'),
                function ($model) {
                    return URL::to("stores/{$model->public_id}/edit");
                },
            ],
            [
                trans('texts.clone_store'),
                function ($model) {
                    return URL::to("stores/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_STORE);
                },
            ],
        ];
    }
}
