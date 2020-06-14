<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model PermissionGroup.
 */
class PermissionGroup extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\PermissionGroupPresenter';
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

    protected $hidden = ['permissions'];

    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_PERMISSION_GROUP;
    }

    public function getRoute()
    {
        return "/permission_groups/{$this->public_id}/edit";
    }

    public function decodePermissions()
    {
        return json_decode($this->permissions, true);
    }

    public static function findPermissionGroupByKey($key)
    {
        return self::Scope()->where('name', '=', $key)->first();
    }

    public function users()
    {
        return $this->belongsToMany('\App\Models\User', 'users_groups', 'group_id', 'user_id');
    }

}
