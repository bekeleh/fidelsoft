<?php

namespace App\Ninja\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends BaseRepository
{
    private $model;

    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Subscription';
    }

    public function find($accountId = false)
    {
        $query = DB::table('subscriptions')
            ->where('subscriptions.account_id', '=', $accountId)
            ->whereNull('subscriptions.deleted_at')
            ->select(
                'subscriptions.public_id',
                'subscriptions.target_url as target',
                'subscriptions.event_id as event',
                'subscriptions.deleted_at',
                'subscriptions.format',
                'subscriptions.created_at',
                'subscriptions.updated_at',
                'subscriptions.deleted_at',
                'subscriptions.created_by',
                'subscriptions.updated_by',
                'subscriptions.deleted_by'
            );

        return $query;
    }
}
