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
                'id_number',
                function ($model) {
                    return $model->id_number;
                },
            ],
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_VENDOR])) {
                        $str = link_to("vendors/{$model->public_id}", $model->vendor_name ?: '')->toHtml();
                        return $this->addNote($str, $model->private_notes);
                    } else {
                        $model->vendor_name;
                    }
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
            [
                'contact',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_VENDOR_CONTACT])) {
                        return link_to("vendor_contacts/{$model->contact_public_id}",
                            $model->contact ?: '')->toHtml();
                    } else {
                        return $model->contact;
                    }
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
                'public_notes',
                function ($model) {
                    return $model->public_notes;
                },
            ],
            [
                'city',
                function ($model) {
                    return $model->city;
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
