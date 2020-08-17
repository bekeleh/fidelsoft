<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturerProductDetails extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;


    protected $presenter = 'App\Ninja\Presenters\ManufacturerProductDetailsPresenter';


    protected $fillable = [
        'manufacturer_id',
        'part_number',
        'upc',
        'serialized',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $table = 'manufacturer_product_details';

    public function getEntityType()
    {
        return ENTITY_MANUFACTURER_PRODUCT_DETAIL;
    }

    public function getRoute()
    {
        return "/manufacturer_product_details/{$this->public_id}/edit";
    }

    public function manufacturer()
    {
        return $this->belongsTo('Modules\Manufacturer\Models\Manufacturer');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
