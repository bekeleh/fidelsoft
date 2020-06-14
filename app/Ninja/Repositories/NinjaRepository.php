<?php

namespace App\Ninja\Repositories;

use App\Models\Account;

class NinjaRepository
{
    private $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function updatePlanDetails($clientPublicId, $data)
    {
        $account = Account::whereId($clientPublicId)->first();

        if (!$account) {
            return;
        }

        $company = $account->company;
        $company->fill($data);
        $company->plan_expires = $company->plan_expires ?: null;

        $company->save();
    }
}
