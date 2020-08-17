<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model PermissionGroup.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property string|null $permissions
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newQuery()
 * @method static Builder|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|PermissionGroup withTrashed()
 * @method static Builder|PermissionGroup withoutTrashed()
 * @mixin Eloquent
 */
class PermissionGroup extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\PermissionGroupPresenter';

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
        return self::scope()->where('name', '=', $key)->first();
    }

    public function users()
    {
        return $this->belongsToMany('\App\Models\User', 'users_groups', 'group_id', 'user_id');
    }

}
