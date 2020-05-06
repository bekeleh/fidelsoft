<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class VendorDatatable extends EntityDatatable
{
    public $entityType = ENTITY_VENDOR;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR])) {
                        $str = link_to("vendors/{$model->public_id}", $model->vendor_name ?: '')->toHtml();
                        return $this->addNote($str, $model->private_notes);
                    } else {
                        $model->vendor_name;
                    }
                },
            ],
            [
                'city',
                function ($model) {
                    return $model->city;
                },
            ],
            [
                'work_phone',
                function ($model) {
                    return $model->work_phone;
                },
            ],
            [
                'email',
                function ($model) {
                    return link_to("vendors/{$model->public_id}", $model->email ?: '')->toHtml();
                },
            ],
            [
                'created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_vendor'),
                function ($model) {
                    return URL::to("vendors/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_VENDOR, $model]);
                },
            ],
            [
                trans('texts.clone_vendor'),
                function ($model) {
                    return URL::to("vendors/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_VENDOR);
                },
            ],
            [
                trans('texts.enter_expense'),
                function ($model) {
                    return URL::to("expenses/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_EXPENSE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_EXPENSE]);
                },
            ],
        ];
    }
}
