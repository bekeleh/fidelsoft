<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 */
class ItemPrice extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ItemPricePresenter';

    protected $table = 'item_prices';
    /**
     * @var array
     */
//    protected $dates = ['start_date', 'end_date', 'deleted_at'];
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'sale_type_id',
        'item_price',
        'start_date',
        'end_date',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            'name',
            'notes',
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'name' => 'name',
            'notes|description|details' => 'notes',
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ITEM_PRICE;
    }

    public static function findItemPriceByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id')->withTrashed();
    }

    public function saleType()
    {
        return $this->belongsTo('App\Models\SaleType', 'sale_type_id')->withTrashed();
    }
}
