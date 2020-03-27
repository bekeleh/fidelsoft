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

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getClassName()
    {
        return 'App\Models\PaymentTerm';
    }

    public function find($accountId = 0)
    {
        return DB::table('payment_terms')
            ->where('payment_terms.account_id', '=', $accountId)
            ->where('payment_terms.deleted_at', '=', null)
            ->select('payment_terms.public_id', 'payment_terms.name', 'payment_terms.num_days', 'payment_terms.deleted_at');
    }
}
