<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Manufacturer extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;


    protected $presenter = 'App\Ninja\Presenters\ManufacturerPresenter';


    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $table = 'manufacturers';

    public function getEntityType()
    {
        return ENTITY_MANUFACTURER;
    }

    public function getRoute()
    {
        return "/manufacturers/{$this->public_id}/edit";
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function manufacturerProductDetails()
    {
        return $this->hasMany('App\Models\ManufacturerProductDetails');
    }
}
