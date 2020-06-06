<?php

namespace App\Ninja\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
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

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('subscriptions')
            ->where('subscriptions.account_id', '=', $accountId)
//            ->whereNull('subscriptions.deleted_at')
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

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('subscriptions.target_url', 'like', '%' . $filter . '%')
                    ->orWhere('subscriptions.created_by', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_SUBSCRIPTION);

        return $query;
    }

    public function save($publicId, $data, $subscription = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($subscription) {
            $subscription->updated_by = Auth::user()->username;

        } elseif ($publicId) {
            $subscription = Subscription::scope($data['public_id'])->firstOrFail();
        } else {
            $subscription = Subscription::createNew();
            $subscription->created_by = Auth::user()->username;
        }

        $subscription->fill($data);

        $subscription->save();

        return $subscription;
    }
}
