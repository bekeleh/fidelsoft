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
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function getImportColumns()
    {
        return [
            'name',
            'notes',
        ];
    }

    public static function getImportMap()
    {
        return [
            'name' => 'name',
            'notes|description|details' => 'notes',
        ];
    }

    public function getEntityType()
    {
        return ENTITY_SALE_TYPE;
    }

    public function itemPrices()
    {
        return $this->hasMany('App\Models\ItemPrice');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }
}
