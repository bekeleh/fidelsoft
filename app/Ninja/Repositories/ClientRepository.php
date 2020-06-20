<?php

namespace App\Ninja\Repositories;

use App\Events\ClientWasCreated;
use App\Events\ClientWasUpdated;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Contact;
use App\Models\HoldReason;
use App\Models\SaleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClientRepository extends BaseRepository
{
    private $model;

    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Client';
    }

    public function getById($publicId, $accountId)
    {
        return $this->model->withTrashed()->where('public_id', $publicId)->where('account_id', $accountId)->first();
    }

    public function all()
    {
        return Client::scope()
            ->with('user', 'contacts', 'country')
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('clients')
            ->join('accounts', 'accounts.id', '=', 'clients.account_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->leftJoin('client_types', 'client_types.id', '=', 'clients.client_type_id')
            ->leftJoin('sale_types', 'sale_types.id', '=', 'clients.sale_type_id')
            ->leftJoin('hold_reasons', 'hold_reasons.id', '=', 'clients.hold_reason_id')
            ->where('clients.account_id', '=', $accountId)
            ->where('contacts.is_primary', '=', true)
//            ->where('hold_reasons.allow_invoice', '=', true)
//            ->where('contacts.deleted_at', '=', null)
//            ->whereRaw('(clients.name != "" or contacts.first_name != "" or contacts.last_name != "" or contacts.email != "")') // filter out buy now invoices
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                DB::raw("CONCAT(COALESCE(contacts.first_name, ''), ' ', COALESCE(contacts.last_name, '')) contact"),
                'clients.public_id',
                'clients.name as client_name',
                'clients.private_notes',
                'contacts.first_name',
                'contacts.last_name',
                'clients.balance',
                'clients.last_login',
                'clients.created_at',
                'clients.created_at as client_created_at',
                'clients.work_phone',
                'contacts.email',
                'clients.deleted_at',
                'clients.is_deleted',
                'clients.user_id',
                'clients.id_number',
                'clients.created_at',
                'clients.updated_at',
                'clients.deleted_at',
                'clients.created_by',
                'clients.updated_by',
                'clients.deleted_by',
                'client_types.name as client_type_name',
                'sale_types.name as sale_type_name',
                'hold_reasons.name as hold_reason_name',

            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('clients.id_number', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('client_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('sale_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('hold_reasons.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_CLIENT);

        return $query;
    }

    public function purge($client)
    {
//        dispatch(new PurgeClientData($client));
    }

    public function findClientType($clintTypePublicId)
    {
        $clientTypeId = ClientType::getPrivateId($clintTypePublicId);

        $query = $this->find()->where('clients.client_type_id', '=', $clientTypeId);

        return $query;
    }

    public function findSaleType($saleTypePublicId)
    {
        $saleTypeId = SaleType::getPrivateId($saleTypePublicId);

        $query = $this->find()->where('clients.sale_type_id', '=', $saleTypeId);

        return $query;
    }

    public function findHoldReason($holdReasonPublicId)
    {
        $holdReasonId = HoldReason::getPrivateId($holdReasonPublicId);

        $query = $this->find()->where('clients.hold_reason_id', '=', $holdReasonId);

        return $query;
    }

    public function save($data, $client = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($client) {
            $client->updated_by = Auth::user()->username;

        } elseif (!$publicId || intval($publicId) < 0) {
            $client = Client::createNew();
            $client->created_by = Auth::user()->username;

        } else {
            $client = Client::scope($publicId)->with('contacts')->firstOrFail();
        }

        // auto-set the client id number
        if (Auth::check() && Auth::user()->account->client_number_counter && !$client->id_number && empty($data['id_number'])) {
            $data['id_number'] = Auth::user()->account->getNextNumber();
        }

        if ($client->is_deleted) {
            return $client;
        }

        // convert currency code to id
        if (isset($data['currency_code'])) {
            $currencyCode = strtolower($data['currency_code']);
            $currency = Cache::get('currencies')->filter(function ($item) use ($currencyCode) {
                return strtolower($item->code) == $currencyCode;
            })->first();
            if ($currency) {
                $data['currency_id'] = $currency->id;
            }
        }

        // convert country code to id
        if (isset($data['country_code'])) {
            $countryCode = strtolower($data['country_code']);
            $country = Cache::get('countries')->filter(function ($item) use ($countryCode) {
                return strtolower($item->iso_3166_2) == $countryCode || strtolower($item->iso_3166_3) == $countryCode;
            })->first();
            if ($country) {
                $data['country_id'] = $country->id;
            }
        }

        // convert shipping country code to id
        if (isset($data['shipping_country_code'])) {
            $countryCode = strtolower($data['shipping_country_code']);
            $country = Cache::get('countries')->filter(function ($item) use ($countryCode) {
                return strtolower($item->iso_3166_2) == $countryCode || strtolower($item->iso_3166_3) == $countryCode;
            })->first();
            if ($country) {
                $data['shipping_country_id'] = $country->id;
            }
        }

        // set default payment terms
        if (auth()->check() && !isset($data['payment_terms'])) {
            $data['payment_terms'] = auth()->user()->account->payment_terms;
        }
        $client->fill($data);
        $client->name = isset($data['name']) ? trim($data['name']) : '';
        $client->save();
        /*
        if ( ! isset($data['contact']) && ! isset($data['contacts'])) {
            return $client;
        }
        */

        $first = true;
        $contacts = isset($data['contact']) ? [$data['contact']] : (isset($data['contacts']) ? $data['contacts'] : [[]]);
        $contactIds = [];

        // If the primary is set ensure it's listed first
        usort($contacts, function ($left, $right) {
            if (isset($right['is_primary']) && isset($left['is_primary'])) {
                return $right['is_primary'] - $left['is_primary'];
            } else {
                return 0;
            }
        });

        foreach ($contacts as $contact) {
            $contact = $client->addContact($contact, $first);
            $contactIds[] = $contact->public_id;
            $first = false;
        }

        if (!$client->wasRecentlyCreated) {
            foreach ($client->contacts as $contact) {
                if (!in_array($contact->public_id, $contactIds)) {
                    $contact->delete();
                }
            }
        }

        if (!$publicId || intval($publicId) < 0) {
            event(new ClientWasCreated($client));
        } else {
            event(new ClientWasUpdated($client));
        }

        return $client;
    }

    public function findPhonetically($clientName)
    {
        $clientNameMeta = metaphone($clientName);

        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $clientId = 0;

        $clients = Client::scope()->get(['id', 'name', 'public_id']);

        foreach ($clients as $client) {

            $map[$client->id] = $client;

            if (!$client->name) {
                continue;
            }

            $similar = similar_text($clientNameMeta, metaphone($client->name), $percent);

            if ($percent > $max) {
                $clientId = $client->id;
                $max = $percent;
            }
        }

        $contacts = Contact::scope()->get(['client_id', 'first_name', 'last_name', 'public_id']);

        foreach ($contacts as $contact) {
            if (!$contact->getFullName() || !isset($map[$contact->client_id])) {
                continue;
            }

            $similar = similar_text($clientNameMeta, metaphone($contact->getFullName()), $percent);

            if ($percent > $max) {
                $clientId = $contact->client_id;
                $max = $percent;
            }
        }

        return ($clientId && isset($map[$clientId])) ? $map[$clientId] : null;
    }
}
