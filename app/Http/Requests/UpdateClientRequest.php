<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\HoldReason;
use App\Models\SaleType;

class UpdateClientRequest extends ClientRequest
{
    protected $entityType = ENTITY_CLIENT;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $client = $this->entity();
        if ($client) {
            $rules['id_number'] = 'required|unique:clients,id_number,' . $client->id . ',id,account_id,' . $client->account_id;
        }
        $rules['name'] = 'required';
        $rules['currency_id'] = 'required|numeric';
        $rules['sale_type_id'] = 'required|numeric';
        $rules['hold_reason_id'] = 'numeric';
        $rules['country_id'] = 'numeric';
        $rules['industry_id'] = 'numeric';
        $rules['size_id'] = 'numeric';
        $rules['language_id'] = 'numeric';
        $rules['shipping_country_id'] = 'numeric';
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (count($input)) {
            if (!empty($input['name'])) {
                $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['id_number'])) {
                $input['id_number'] = filter_var($input['id_number'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['country_id'])) {
                $input['country_id'] = filter_var($input['country_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['currency_id'])) {
                $input['currency_id'] = filter_var($input['currency_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['sale_type_id'])) {
                $input['sale_type_id'] = filter_var($input['sale_type_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['hold_reason_id'])) {
                $input['hold_reason_id'] = filter_var($input['hold_reason_id'], FILTER_SANITIZE_NUMBER_INT);
            }

            if (!empty($input['industry_id'])) {
                $input['industry_id'] = filter_var($input['industry_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['size_id'])) {
                $input['size_id'] = filter_var($input['size_id'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['language_id'])) {
                $input['language_id'] = filter_var($input['language_id'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['shipping_country_id'])) {
                $input['shipping_country_id'] = filter_var($input['shipping_country_id'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['notes'])) {
                $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
            }


            $this->replace($input);
        }
    }

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
//            if (!empty($input['sale_type_id'])) {
//                $input['sale_type_id'] = SaLeType::getPrivateId($input['sale_type_id']);
//            }
//            if (!empty($input['hold_reason_id'])) {
//                $input['hold_reason_id'] = HoldReason::getPrivateId($input['hold_reason_id']);
//            }

            if (!empty($input['sale_type_id']) && !empty($input['hold_reason_id'])) {
                $this->request->add([
//                    'sale_type_id' => $input['sale_type_id'],
//                    'hold_reason_id' => $input['hold_reason_id'],
                    'account_id' => Client::getAccountId()
                ]);
            }
        }

        return $this->request->all();
    }
}
