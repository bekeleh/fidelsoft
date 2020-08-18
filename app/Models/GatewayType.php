<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Libraries\Utils;

/**
 * Class GatewayType.
 *
 * @property int $id
 * @property string $alias
 * @property string $name
 * @method static Builder|GatewayType newModelQuery()
 * @method static Builder|GatewayType newQuery()
 * @method static Builder|GatewayType query()
 * @method static Builder|GatewayType whereAlias($value)
 * @method static Builder|GatewayType whereId($value)
 * @method static Builder|GatewayType whereName($value)
 * @mixin \Eloquent
 */
class GatewayType extends Eloquent
{
    protected $table = 'gateway_types';
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public static function getAliasFromId($id)
    {
//            return Utils::getFromCache($id, 'gatewayTypes')->alias;
    }

    public static function getIdFromAlias($alias)
    {
        $data = Cache::get('gatewayTypes')->where('alias', $alias)->first()->id;
        if (!$data) {
            return false;
        }

        return $data;
    }
}
