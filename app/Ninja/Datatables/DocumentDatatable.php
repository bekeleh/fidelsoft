<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class DocumentDatatable extends EntityDatatable
{
    public $entityType = ENTITY_DOCUMENT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'document_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_DOCUMENT]))
                        return link_to("documents/{$model->public_id}", $model->document_name ?: '')->toHtml();
                    else
                        return $model->document_name;
                },
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
                uctrans('texts.edit_documents'),
                function ($model) {
                    return URL::to("documents/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_DOCUMENT);
                },
            ],
            [
                uctrans('texts.clone_documents'),
                function ($model) {
                    return URL::to("documents/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_DOCUMENT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_DOCUMENT]);
                },
            ],
        ];
    }
}
