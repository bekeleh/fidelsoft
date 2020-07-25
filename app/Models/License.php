<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Class License.
 */
class License extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = true;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
}
