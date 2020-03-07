<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 */
class SaleType extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\SaleTypePresenter';

    protected $table = 'sale_types';
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'notes',
        'custom_value1',
        'custom_value2',
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
        return ENTITY_SALE_TYPE;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function itemPrices()
    {
        return $this->belongsMany('App\Models\ItemPrice')->withTrashed();
    }
}
