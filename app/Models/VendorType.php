<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class VendorType.
 */
class VendorType extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\VendorTypePresenter';

    protected $table = 'vendor_types';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'name',
        'is_deleted',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_CLIENT_TYPE;
    }

    public function vendors()
    {
        return $this->hasMany('App\Models\Vendor')->withTrashed();
    }

    public function itemPrices()
    {
        return $this->hasMany('App\Models\ItemPrice')->withTrashed();
    }

    public static function selectOptions()
    {
        $types = VendorType::where('account_id', null)->get();

        foreach (VendorType::scope()->get() as $type) {
            $types->push($type);
        }

        foreach($types as $type){
            $name = Str::snake(str_replace(' ', '_', $type->name));
            $types->name = trans('texts.vendor_type_' . $name);
        }

        return $types->sortBy('name');
    }
    
}
