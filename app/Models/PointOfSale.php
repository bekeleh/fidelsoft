<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * App\Models\PointOfSale
 *
 * @property-read Account $account
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PointOfSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PointOfSale newQuery()
 * @method static Builder|PointOfSale onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PointOfSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|PointOfSale withTrashed()
 * @method static Builder|PointOfSale withoutTrashed()
 * @mixin Eloquent
 */
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
