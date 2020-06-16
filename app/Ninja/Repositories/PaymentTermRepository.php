<?php

namespace App\Ninja\Repositories;

use App\Models\PaymentTerm;
use Illuminate\Support\Facades\DB;

class PaymentTermRepository extends BaseRepository
{
    private $model;

    public function __construct(PaymentTerm $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\PaymentTerm';
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('payment_terms')
            ->where('payment_terms.account_id', '=', $accountId)
            ->where('payment_terms.deleted_at', '=', null)
            ->select(
                'payment_terms.public_id',
                'payment_terms.name',
                'payment_terms.num_days',
                'payment_terms.created_at',
                'payment_terms.updated_at',
                'payment_terms.deleted_at',
                'payment_terms.created_by',
                'payment_terms.updated_by',
                'payment_terms.deleted_by'
            );

        if (!$filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('payment_terms.name', 'like', '%' . $filter . '%')
                    ->orwhere('payment_terms.created_by', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_PAYMENT_TERM);

        return $query;
    }
}
