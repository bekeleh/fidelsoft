<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemTransferDataTable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_TRANSFER;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
//            [
//                'id',
//                function ($model) {
//                    return $model->id;
//                },
//            ],
            [
                'item_name',
                function ($model) {
                    if ($model->product_public_id) {
                        if (Auth::user()->can('view', [ENTITY_PRODUCT, $model]))
                            return link_to("products/{$model->product_public_id}", $model->item_name)->toHtml();
                        else
                            return $model->item_name;
                    }
                    return false;
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    if ($model->item_brand_public_id) {
                        if (Auth::user()->can('view', [ENTITY_ITEM_BRAND, $model]))
                            return link_to("item_brands/{$model->item_brand_public_id}", $model->item_brand_name)->toHtml();
                        else
                            return $model->item_brand_name;
                    }
                    return false;
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    if ($model->item_category_public_id) {
                        if (Auth::user()->can('view', [ENTITY_ITEM_CATEGORY, $model]))
                            return link_to("item_categories/{$model->item_category_public_id}", $model->item_category_name)->toHtml();
                        else
                            return $model->item_category_name;
                    }
                    return false;
                },
            ],
            [
                'from_store_name',
                function ($model) {
                    if ($model->from_store_public_id) {
                        if (Auth::user()->can('view', [ENTITY_STORE, $model]))
                            return link_to("stores/{$model->from_store_public_id}", $model->from_store_name)->toHtml();
                        else
                            return $model->from_store_name;
                    }
                    return false;
                },
            ],
            [
                'to_store_name',
                function ($model) {
                    if ($model->to_store_public_id) {
                        if (Auth::user()->can('view', [ENTITY_STORE, $model]))
                            return link_to("stores/{$model->to_store_public_id}", $model->to_store_name)->toHtml();
                        else
                            return $model->to_store_name;
                    }
                    return false;
                },
            ],
            [
                'qty',
                function ($model) {
                    return self::getStatusLabel($model);
                },
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
                },
            ],
//            [
//                'status_name',
//                function ($model) {
//                    if ($model->status_public_id) {
//                        if (Auth::user()->can('view', [ENTITY_STATUS, $model]))
//                            return link_to("statuses/{$model->status_public_id}", $model->status_name)->toHtml();
//                        else
//                            return $model->status_name;
//                    }
//                    return false;
//                },
//            ],
//            [
//                'approver_name',
//                function ($model) {
//                    if ($model->approver_public_id) {
//                        if (Auth::user()->can('view', [ENTITY_USER, $model]))
//                            return link_to("users/{$model->approver_public_id}", $model->approver_name)->toHtml();
//                        else
//                            return $model->approver_name;
//                    }
//                    return false;
//                },
//            ],
            [
                'created_by',
                function ($model) {
                    return $model->created_by;
                },
            ],
//            [
//                'updated_by',
//                function ($model) {
//                    return $model->updated_by;
//                },
//            ],
//            [
//                'dispatch_date',
//                function ($model) {
//                    return Utils::timestampToDateString(strtotime($model->dispatch_date));
//                },
//            ],
            [
                'created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
//            [
//                'updated_at',
//                function ($model) {
//                    return Utils::timestampToDateString(strtotime($model->updated_at));
//                },
//            ],
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
                uctrans('texts.edit_item_transfer'),
                function ($model) {
                    return URL::to("item_transfers/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_ITEM_TRANSFER);
                },
            ],
//            [
//                trans('texts.clone_item_transfer'),
//                function ($model) {
//                    return URL::to("item_transfers/{$model->public_id}/clone");
//                },
//                function ($model) {
//                    return Auth::user()->can('create', ENTITY_ITEM_TRANSFER);
//                },
//            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemTransfer::calcStatusClass($model->qty, 0);

        return "<h4><div class=\"label label-{$class}\">$model->qty</div></h4>";
    }
}
