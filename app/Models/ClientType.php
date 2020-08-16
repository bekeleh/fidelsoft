<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ClientType.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Collection|ItemPrice[] $itemPrices
 * @property-read int|null $item_prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType newQuery()
 * @method static Builder|ClientType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ClientType withTrashed()
 * @method static Builder|ClientType withoutTrashed()
 * @mixin Eloquent
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
