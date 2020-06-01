<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class PointOfSale extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\PointOfSalePresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'item_serial',
        'item_barcode',
        'item_tag',
        'notes',
        'cost',
        'item_brand_id',
        'unit_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];

    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }


}
