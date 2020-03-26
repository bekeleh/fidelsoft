<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model Group.
 */
class Group extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\GroupPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];

    protected $table = 'permission_groups';

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'permissions',
        'is_deleted',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_GROUP;
    }

    public static function findGroupByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function decodePermissions()
    {
        return json_decode($this->permissions, true);
    }

    public function decodeGroups()
    {
        return json_decode($this->groups, true);
    }

    public function users()
    {
        return $this->belongsToMany('\App\Models\User', 'users_groups', 'group_id', 'user_id');
    }


    public function getRoute()
    {
        return "/groups/{$this->public_id}/edit";
    }

}
