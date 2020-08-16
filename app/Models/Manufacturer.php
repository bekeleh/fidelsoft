<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * App\Models\Manufacturer
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|ManufacturerProductDetails[] $manufacturerProductDetails
 * @property-read int|null $manufacturer_product_details_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newQuery()
 * @method static Builder|Manufacturer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Manufacturer withTrashed()
 * @method static Builder|Manufacturer withoutTrashed()
 * @mixin Eloquent
 */
class Manufacturer extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;


    protected $presenter = 'App\Ninja\Presenters\ManufacturerPresenter';


    protected $fillable = [
        'name',
        'notes',
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
