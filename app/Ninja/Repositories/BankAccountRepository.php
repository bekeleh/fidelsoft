<?php

namespace App\Ninja\Repositories;

use App\Models\BankAccount;
use App\Models\BankSubaccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BankAccountRepository extends BaseRepository
{
    private $model;

    public function __construct(BankAccount $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\BankAccount';
    }

    public function all()
    {
        return BankAccount::scope()
        ->withTrashed()
        ->where('is_deleted', '=', false)
        ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('bank_accounts')
        ->leftJoin('banks', 'banks.id', '=', 'bank_accounts.bank_id')
        ->where('bank_accounts.account_id', '=', $accountId)
        // ->whereNull('bank_accounts.deleted_at')
        ->select(
            'bank_accounts.public_id',
            'banks.name as bank_name',
            'banks.public_id as bank_public_id',
            'bank_accounts.deleted_at',
            'banks.bank_library_id',
            'bank_accounts.created_at',
            'bank_accounts.updated_at',
            'bank_accounts.deleted_at',
            'bank_accounts.created_by',
            'bank_accounts.updated_by',
            'bank_accounts.deleted_by'
        );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('bank_accounts.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_BANK_ACCOUNT);

        return $query;
    }

    public function save($input)
    {
        $bankAccount = BankAccount::createNew();
        $bankAccount->username = Crypt::encrypt(trim($input['bank_username']));
        $bankAccount->fill($input);

        $account = Auth::user()->account;
        $account->bank_accounts()->save($bankAccount);

        foreach ($input['bank_accounts'] as $data) {
            if (!isset($data['include']) || !filter_var($data['include'], FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            $subaccount = BankSubaccount::createNew();
            $subaccount->account_name = trim($data['account_name']);
            $subaccount->account_number = trim($data['hashed_account_number']);
            $bankAccount->bank_subaccounts()->save($subaccount);
        }

        return $bankAccount;
    }
}
