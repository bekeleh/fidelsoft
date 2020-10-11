<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 */
class Subscription extends EntityModel
{
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\SubscriptionPresenter';

    protected $table = 'subscriptions';
    public $timestamps = true;

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

    public function getRoute()
    {
        return "/subscriptions/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function notify($notification, $event)
    {
        $this->user->whereHas('subscriber');
    }

//   can be optimized
    public static function subscriber($eventId)
    {
        return DB::table('subscriptions')
            ->where('event_id', $eventId)
            ->pluck('user_id');
    }


}
