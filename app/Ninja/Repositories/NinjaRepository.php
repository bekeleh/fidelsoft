<?php

namespace App\Ninja\Repositories;

use App\Models\Account;
use App\Models\Company;
use Exception;

class NinjaRepository
{
    private $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function updatePlanDetails($AccountId, $data)
    {
        try{
            $account = Account::whereId($AccountId)->first();
            $company = $account->company;

            if (!isset($account)) {
                return false;
            }
            $company->fill($data);
            $company->plan_expires = $company->plan_expires ?: null;

            if($company->save()){
                return true;
            }

            return false;
        } catch (Exception $e) {
            // show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
