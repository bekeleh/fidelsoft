<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Libraries\Utils;

/**
 * Class GatewayType.
 */
class GatewayType extends Eloquent
{
    /**
     * @var bool
     */
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
            return null;
        }

        return $data;
    }
}
