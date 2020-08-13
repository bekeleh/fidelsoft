<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class PurchaseOrderStatusService.
 */
class billstatus extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;
    protected $presenter = 'App\Ninja\Presenters\PurchaseOrderStatusPresenter';

    protected $dates = ['created_at', 'deleted_at'];
    protected $hidden = ['deleted_at'];
    protected $casts = [];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_BILL_STATUS;
    }

    public function getRoute()
    {
        return "/purchase_order_statuses/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

}
