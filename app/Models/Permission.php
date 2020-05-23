<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model Permission.
 */
class Permission extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\PermissionPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];


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
        return ENTITY_PERMISSION;
    }

    public function getRoute()
    {
        return "/permissions/{$this->public_id}/edit";
    }

}
