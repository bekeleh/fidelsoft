<?php

namespace App\Ninja\Repositories;

use App\Models\Common\AccountGatewayToken;
use App\Models\PaymentMethod;
use App\Models\Vendor;

class CustomerRepository extends BaseRepository
{
    private $model;

    public function __construct(Vendor $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Common\AccountGatewayToken';
    }

    public function all()
    {
        return AccountGatewayToken::whereAccountId(auth()->user()->account_id)
            ->with(['contact'])
            ->get();
    }

    public function save($data)
    {
        $account = auth()->user()->account;

        $customer = new AccountGatewayToken();
        $customer->account_id = $account->id;
        $customer->fill($data);
        $customer->save();

        $paymentMethod = PaymentMethod::createNew();
        $paymentMethod->account_gateway_token_id = $customer->id;
        $paymentMethod->fill($data['payment_method']);
        $paymentMethod->save();

        $customer->default_payment_method_id = $paymentMethod->id;

        $customer->save();

        return $customer;
    }
}
