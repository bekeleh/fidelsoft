<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class StatusService.
 */
class Status extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;
    protected $presenter = 'App\Ninja\Presenters\StatusPresenter';

    protected $appends = [];
    protected $table = 'statuses';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];

    public function getEntityType()
    {
        return ENTITY_STATUS;
    }

    public function getRoute()
    {
        return "/statuses/{$this->public_id}/edit";
    }

    public static function findStatusByKey($key)
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

    public function itemTransfers()
    {
        return $this->hasMany('App\Models\ItemTransfer', 'status_id')->withTrashed();
    }
}
