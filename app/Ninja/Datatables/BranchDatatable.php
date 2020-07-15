<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class BranchDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BRANCH;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'branch_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_BRANCH])){
                        return link_to("branches/{$model->public_id}", $model->branch_name ?: '')->toHtml();
                    }else{
                        return $model->branch_name;
                    }  
                },
            ],
            [
                'store_name',
                function ($model) {
                    if ($model->store_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_STORE]))
                            return link_to("stores/{$model->store_public_id}", $model->store_name ?: '')->toHtml();
                        else
                            return $model->store_name;
                    } else {
                        return $model->store_name;
                    }
                },
            ],
            [
                'location_name',
                function ($model) {
                    if ($model->location_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_LOCATION]))
                            return link_to("locations/{$model->location_public_id}", $model->location_name ?: '')->toHtml();
                        else
                            return $model->location_name;
                    } else {
                        return $model->location_name;
                    }
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
                uctrans('texts.edit_branch'),
                function ($model) {
                    return URL::to("branches/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_BRANCH);
                },
            ],
            [
                uctrans('texts.clone_branch'),
                function ($model) {
                    return URL::to("branches/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_BRANCH);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BRANCH]);
                },
            ],
        ];
    }
}
