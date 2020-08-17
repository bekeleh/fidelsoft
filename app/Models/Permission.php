<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model Permission.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static Builder|Permission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Permission withTrashed()
 * @method static Builder|Permission withoutTrashed()
 * @mixin Eloquent
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
