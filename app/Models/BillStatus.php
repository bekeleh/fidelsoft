<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class PurchaseOrderStatusService.
 */
class BillStatus extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;
    protected $presenter = 'App\Ninja\Presenters\PurchaseOrderStatusPresenter';

    protected $appends = [];
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];

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
