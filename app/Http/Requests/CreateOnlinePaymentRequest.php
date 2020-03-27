<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\GatewayType;

class CreateOnlinePaymentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $account = $this->invitation->account;

        $paymentDriver = $account->paymentDriver($this->invitation, $this->gateway_type);

        return $paymentDriver->rules();
    }

    public function sanitize()
    {
        $input = $this->all();

        $invitation = Invitation::with('invoice.invoice_items', 'invoice.client.currency', 'invoice.client.account.currency', 'invoice.client.account.account_gateways.gateway')
            ->where('invitation_key', '=', $this->invitation_key)
            ->firstOrFail();

        $input['invitation'] = $invitation;

        if ($gatewayTypeAlias = request()->gateway_type) {
            $input['gateway_type'] = GatewayType::getIdFromAlias($gatewayTypeAlias);
        } else {
            $input['gateway_type'] = session($invitation->id . 'gateway_type');
        }

        $this->replace($input);

        return $this->all();
    }
}
