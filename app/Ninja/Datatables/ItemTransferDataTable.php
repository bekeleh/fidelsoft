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
            [
                'item_name',
                function ($model) {
                    if ($model->public_id) {
                        if (Auth::user()->can('view', [ENTITY_PRODUCT, $model]))
                            return link_to("products/{$model->public_id}", $model->item_name)->toHtml();
                        else
                            return $model->item_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'item_brand_name',
                function ($model) {
                    if ($model->public_id) {
                        if (Auth::user()->can('view', [ENTITY_ITEM_BRAND, $model]))
                            return link_to("item_brands/{$model->public_id}", $model->item_brand_name)->toHtml();
                        else
                            return $model->item_brand_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'item_category_name',
                function ($model) {
                    if ($model->public_id) {
                        if (Auth::user()->can('view', [ENTITY_ITEM_CATEGORY, $model]))
                            return link_to("item_categories/{$model->public_id}", $model->item_category_name)->toHtml();
                        else
                            return $model->item_category_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'from_store_name',
                function ($model) {
                    if ($model->store_id) {
                        if (Auth::user()->can('view', [ENTITY_STORE, $model]))
                            return link_to("stores/{$model->public_id}", $model->from_store_name)->toHtml();
                        else
                            return $model->from_store_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'to_store_name',
                function ($model) {
                    if ($model->store_id) {
                        if (Auth::user()->can('view', [ENTITY_STORE, $model]))
                            return link_to("stores/{$model->public_id}", $model->to_store_name)->toHtml();
                        else
                            return $model->to_store_name;
                    } else {
                        return '';
                    }
                }
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
            [
                'approver_name',
                function ($model) {
                    if ($model->user_id) {
                        if (Auth::user()->can('view', [ENTITY_USER, $model]))
                            return link_to("users/{$model->public_id}", $model->approver_name)->toHtml();
                        else
                            return $model->approver_name;
                    } else {
                        return '';
                    }
                }
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
                'approved_date',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->approved_date));
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
                uctrans('texts.edit_item_transfer'),
                function ($model) {
                    return URL::to("item_transfers/{$model->public_id}/edit");
                },
            ],
            [
                trans('texts.clone_item_transfer'),
                function ($model) {
                    return URL::to("item_transfers/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_TRANSFER);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemTransfer::calcStatusClass($model->qty, $model->reorder_level);

        return "<h4><div class=\"label label-{$class}\">$model->qty</div></h4>";
    }
}
