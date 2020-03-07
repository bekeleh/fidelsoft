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
    protected $dates = ['start_date','end_date','deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'item_id',
        'sales_type_id',
        'price',
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

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('item_id', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function salesType()
    {
        return $this->belongsTo('App\Models\SaleType')->withTrashed();
    }
}
