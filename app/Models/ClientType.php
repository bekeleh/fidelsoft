<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ClientType.
 */
class ClientType extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ClientTypePresenter';

    protected $table = 'client_types';
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

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }

    public function itemPrices()
    {
        return $this->hasMany('App\Models\ItemPrice')->withTrashed();
    }

    public static function selectOptions()
    {
        $types = ClientType::where('account_id', null)->get();

        foreach (ClientType::scope()->get() as $type) {
            $types->push($type);
        }

        foreach($types as $type){
            $name = Str::snake(str_replace(' ', '_', $type->name));
            $types->name = trans('texts.client_type_' . $name);
        }

        return $types->sortBy('name');
    }
}
