<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemTransferDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_TRANSFER;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'product_key',
                function ($model) {
                    if ($model->product_public_id) {
                        return link_to("products/{$model->product_public_id}", $model->product_key)->toHtml();
                    } else {
                        return $model->item_name;
                    }
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    if ($model->item_brand_public_id) {
                        return link_to("item_brands/{$model->item_brand_public_id}", $model->item_brand_name)->toHtml();
                    } else {
                        return $model->item_brand_name;
                    }
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    if ($model->item_category_public_id) {
                        return link_to("item_categories/{$model->item_category_public_id}", $model->item_category_name)->toHtml();
                    } else {
                        return $model->item_category_name;
                    }
                },
            ],
            [
                'from_store_name',
                function ($model) {
                    if ($model->from_store_public_id) {
                        return link_to("warehouses/{$model->from_store_public_id}", $model->from_store_name)->toHtml();
                    } else {
                        return $model->from_store_name;
                    }
                },
            ],
            [
                'to_store_name',
                function ($model) {
                    if ($model->to_store_public_id) {
                        return link_to("warehouses/{$model->to_store_public_id}", $model->to_store_name)->toHtml();
                    } else {
                        return $model->to_store_name;
                    }
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
//                            return link_to("statuses/{$model->status_public_id}", $model->status_name)->toHtml();
//                        }else{
//                            return $model->status_name;
//                    }
//                },
//            ],
//            [
//                'approver_name',
//                function ($model) {
//                    if ($model->approver_public_id) {
//                            return link_to("users/{$model->approver_public_id}", $model->approver_name)->toHtml();
//                        }else{
//                            return $model->approver_name;
//                    }
//                },
//            ],
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
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return auth()->user()->canCreateOrEdit(ENTITY_ITEM_TRANSFER);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemTransfer::calcStatusClass($model->qty, 0);

        return "<h4><div class=\"label label-{$class}\">$model->qty</div></h4>";
    }
}
