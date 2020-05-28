<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 */
class Subscription extends EntityModel
{

    public $timestamps = true;

    use SoftDeletes;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'event_id',
        'target_url',
        'format',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_SUBSCRIPTION;
    }


    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

}
