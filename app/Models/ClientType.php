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

    public static function findClientTypeByKey($key)
    {
        return self::Scope()->where('name', '=', $key)->first();
    }

    public function getEntityType()
    {
        return ENTITY_CLIENT_TYPE;
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }
}
