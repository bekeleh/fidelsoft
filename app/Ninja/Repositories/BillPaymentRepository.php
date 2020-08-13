<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\Credit;
use App\Models\Vendor;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillPaymentRepository extends BaseRepository
{
    private $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Payment';
    }

    public function find($clientPublicId = false, $filter = null)
    {
        $query = DB::table('payments')
            ->leftJoin('accounts', 'accounts.id', '=', 'payments.account_id')
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'payments.vendor_id')
            ->leftJoin('bills', 'bills.id', '=', 'payments.bill_id')
            ->leftJoin('contacts', 'contacts.vendor_id', '=', 'vendors.id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'payments.payment_status_id')
            ->leftJoin('payment_types', 'payment_types.id', '=', 'payments.payment_type_id')
            ->leftJoin('account_gateways', 'account_gateways.id', '=', 'payments.account_gateway_id')
            ->leftJoin('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
            ->where('payments.account_id', '=', Auth::user()->account_id)
            ->where('contacts.is_primary', '=', true)
            ->where('contacts.deleted_at', '=', null)
            ->where('bills.is_deleted', '=', false)
            ->select('payments.public_id',
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'payments.transaction_reference',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'vendors.public_id as client_public_id',
                'vendors.user_id as client_user_id',
                'payments.amount',
                DB::raw("CONCAT(payments.payment_date, payments.created_at) as date"),
                'payments.payment_date',
                'payments.payment_status_id',
                'payments.payment_type_id',
                'payments.payment_type_id as source',
                'bills.public_id as invoice_public_id',
                'bills.user_id as invoice_user_id',
                'bills.bill_number',
                'bills.bill_number as invoice_name',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.email',
                'payment_types.name as method',
                'payment_types.name as payment_type',
                'payments.account_gateway_id',
                'payments.deleted_at',
                'payments.is_deleted',
                'payments.user_id',
                'payments.refunded',
                'payments.expiration',
                'payments.last4',
                'payments.email',
                'payments.routing_number',
                'payments.bank_name',
                'payments.private_notes',
                'payments.public_notes',
                'payments.exchange_rate',
                'payments.exchange_currency_id',
                'bills.is_deleted as invoice_is_deleted',
                'gateways.name as gateway_name',
                'gateways.id as gateway_id',
                'payment_statuses.name as status',
                'payments.created_at',
                'payments.updated_at',
                'payments.deleted_at',
                'payments.created_by',
                'payments.updated_by',
                'payments.deleted_by'
            );

        $this->applyFilters($query, ENTITY_PAYMENT);

        if ($clientPublicId) {
            $query->where('vendors.public_id', '=', $clientPublicId);
        } else {
            $query->whereNull('vendors.deleted_at');
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('bills.bill_number', 'like', '%' . $filter . '%')
                    ->orWhere('payments.transaction_reference', 'like', '%' . $filter . '%')
                    ->orWhere('gateways.name', 'like', '%' . $filter . '%')
                    ->orWhere('payment_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function findForContact($contactId = null, $filter = null)
    {
        $query = DB::table('payments')
            ->join('accounts', 'accounts.id', '=', 'payments.account_id')
            ->join('vendors', 'vendors.id', '=', 'payments.vendor_id')
            ->join('bills', 'bills.id', '=', 'payments.bill_id')
            ->join('contacts', 'contacts.vendor_id', '=', 'vendors.id')
            ->join('payment_statuses', 'payment_statuses.id', '=', 'payments.payment_status_id')
            ->leftJoin('invitations', function ($join) use ($contactId) {
                $join->on('invitations.bill_id', '=', 'bills.id')
                    ->on('invitations.contact_id', '=', 'contacts.id')
                    ->where('invitations.contact_id', '=', $contactId);
            })
            ->leftJoin('payment_types', 'payment_types.id', '=', 'payments.payment_type_id')
            ->where('vendors.is_deleted', '=', false)
            ->where('payments.is_deleted', '=', false)
            ->where('bills.is_deleted', '=', false)
            ->where('bills.is_public', '=', true)
            ->where('invitations.deleted_at', '=', null)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'invitations.invitation_key',
                'invitations.contact_id',
                'payments.public_id',
                'payments.transaction_reference',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'vendors.public_id as client_public_id',
                'payments.amount',
                'payments.payment_date',
                'payments.payment_type_id',
                'bills.public_id as invoice_public_id',
                'bills.bill_number',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.email',
                'payment_types.name as payment_type',
                'payments.account_gateway_id',
                'payments.refunded',
                'payments.expiration',
                'payments.last4',
                'payments.email',
                'payments.routing_number',
                'payments.bank_name',
                'payments.payment_status_id',
                'payment_statuses.name as payment_status_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($input, $payment = null)
    {
        $publicId = isset($input['public_id']) ? $input['public_id'] : false;

        if ($payment) {
            $payment->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $payment = Payment::scope($publicId)->firstOrFail();
        } else {
            $payment = Payment::createNew();
            if (Auth::check() && Auth::user()->account->payment_type_id) {
                $payment->payment_type_id = Auth::user()->account->payment_type_id;
            }
            $payment->created_by = auth::user()->username;
        }

        if ($payment->is_deleted) {
            return $payment;
        }

        $paymentTypeId = false;
        if (isset($input['payment_type_id'])) {
            $paymentTypeId = $input['payment_type_id'] ? $input['payment_type_id'] : null;
            $payment->payment_type_id = $paymentTypeId;
        }

        if (isset($input['payment_status_id'])) {
            $paymentStatusId = $input['payment_status_id'] ? $input['payment_status_id'] : null;
            $payment->payment_status_id = $paymentStatusId;
        }
        if (!isset($input['exchange_currency_id'])) {
            // $client = Vendor::scope()->where('id',$clientId)->first();
            // $payment->exchange_currency_id = ($client->currency_id)? $client->currency_id: null;
        }
        if (isset($input['payment_date_sql'])) {
            $payment->payment_date = $input['payment_date_sql'];
        } elseif (isset($input['payment_date'])) {
            $payment->payment_date = Utils::toSqlDate($input['payment_date']);
        } else {
            $payment->payment_date = date('Y-m-d');
        }

        $payment->fill($input);

        if (!$publicId) {
            $clientId = $input['vendor_id'];
            $amount = round(Utils::parseFloat($input['amount']), 2);
            $amount = min($amount, MAX_INVOICE_AMOUNT);

            if ($paymentTypeId == PAYMENT_TYPE_CREDIT) {
                $credits = Credit::scope()->where('vendor_id', $clientId)
                    ->where('balance', '>', 0)->orderBy('created_at')->get();
                if ($credits->count()) {
                    $remaining = $amount;
                    foreach ($credits as $credit) {
                        $remaining -= $credit->apply($remaining);
                        if (!$remaining) {
                            break;
                        }
                    }
                }
            }

            $payment->bill_id = $input['bill_id'];
            $payment->vendor_id = $clientId;
            $payment->amount = $amount;
        }

        $payment->save();

        return $payment;
    }

    public function delete($payment)
    {
        if ($payment->invoice->is_deleted) {
            return false;
        }

        parent::delete($payment);
    }

    public function restore($payment)
    {
        if ($payment->invoice->is_deleted) {
            return false;
        }

        parent::restore($payment);
    }
}
