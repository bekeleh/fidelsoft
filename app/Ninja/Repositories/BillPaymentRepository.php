<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\VendorCredit;
use App\Models\Vendor;
use App\Models\BillPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillPaymentRepository extends BaseRepository
{
    private $model;

    public function __construct(BillPayment $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\BillPayment';
    }

    public function find($vendorPublicId = false, $filter = null)
    {
        $query = DB::table('bill_payments')
            ->leftJoin('accounts', 'accounts.id', '=', 'bill_payments.account_id')
            ->leftJoin('users', 'users.id', '=', 'bill_payments.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'bill_payments.vendor_id')
            ->leftJoin('bills', 'bills.id', '=', 'bill_payments.bill_id')
            ->leftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'bill_payments.payment_status_id')
            ->leftJoin('payment_types', 'payment_types.id', '=', 'bill_payments.payment_type_id')
            ->leftJoin('account_gateways', 'account_gateways.id', '=', 'bill_payments.account_gateway_id')
            ->leftJoin('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
            ->where('bill_payments.account_id', Auth::user()->account_id)
            ->where('vendor_contacts.is_primary', true)
//            ->where('vendor_contacts.deleted_at', null)
//            ->where('bills.is_deleted', false)
            ->select('bill_payments.public_id',
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'bill_payments.transaction_reference',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'vendors.public_id as vendor_public_id',
                'vendors.user_id as vendor_user_id',
                'bill_payments.amount',
                DB::raw("CONCAT(bill_payments.payment_date, bill_payments.created_at) as date"),
                'bill_payments.payment_date',
                'bill_payments.payment_status_id',
                'bill_payments.payment_type_id',
                'bill_payments.payment_type_id as source',
                'bills.public_id as bill_public_id',
                'bills.user_id as bill_user_id',
                'bills.invoice_number',
                'bills.invoice_number as bill_name',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'payment_types.name as method',
                'payment_types.name as payment_type',
                'bill_payments.account_gateway_id',
                'bill_payments.deleted_at',
                'bill_payments.is_deleted',
                'bill_payments.user_id',
                'bill_payments.refunded',
                'bill_payments.expiration',
                'bill_payments.last4',
                'bill_payments.email',
                'bill_payments.routing_number',
                'bill_payments.bank_name',
                'bill_payments.private_notes',
                'bill_payments.public_notes',
                'bill_payments.exchange_rate',
                'bill_payments.exchange_currency_id',
                'bills.is_deleted as bill_is_deleted',
                'gateways.name as gateway_name',
                'gateways.id as gateway_id',
                'payment_statuses.name as status',
                'bill_payments.created_at',
                'bill_payments.updated_at',
                'bill_payments.deleted_at',
                'bill_payments.created_by',
                'bill_payments.updated_by',
                'bill_payments.deleted_by'
            );

        $this->applyFilters($query, ENTITY_BILL_PAYMENT);

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        } else {
            $query->whereNull('vendors.deleted_at');
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('bills.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('bill_payments.transaction_reference', 'like', '%' . $filter . '%')
                    ->orWhere('gateways.name', 'like', '%' . $filter . '%')
                    ->orWhere('payment_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.phone', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function findForContact($contactId = null, $filter = null)
    {
        $query = DB::table('bill_payments')
            ->leftJoin('accounts', 'accounts.id', '=', 'bill_payments.account_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'bill_payments.vendor_id')
            ->leftJoin('bills', 'bills.id', '=', 'bill_payments.bill_id')
            ->leftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'bill_payments.payment_status_id')
            ->leftJoin('bill_invitations', function ($join) use ($contactId) {
                $join->on('bill_invitations.bill_id', 'bills.id')
                    ->on('bill_invitations.contact_id', 'vendor_contacts.id')
                    ->where('bill_invitations.contact_id', $contactId);
            })
            ->leftJoin('payment_types', 'payment_types.id', '=', 'bill_payments.payment_type_id')
            ->where('bills.is_public', true)
//            ->where('vendors.is_deleted', false)
//            ->where('bill_payments.is_deleted', false)
//            ->where('bills.is_deleted', false)
//            ->where('bill_invitations.deleted_at', null)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'bill_invitations.invitation_key',
                'bill_invitations.contact_id',
                'bill_payments.public_id',
                'bill_payments.transaction_reference',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'vendors.public_id as vendor_public_id',
                'bill_payments.amount',
                'bill_payments.payment_date',
                'bill_payments.payment_type_id',
                'bills.public_id as bill_public_id',
                'bills.invoice_number',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'payment_types.name as payment_type',
                'bill_payments.account_gateway_id',
                'bill_payments.refunded',
                'bill_payments.expiration',
                'bill_payments.last4',
                'bill_payments.email',
                'bill_payments.routing_number',
                'bill_payments.bank_name',
                'bill_payments.payment_status_id',
                'payment_statuses.name as payment_status_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orwhere('bills.invoice_number', 'like', '%' . $filter . '%')
                    ->orwhere('bill_payments.transaction_reference', 'like', '%' . $filter . '%')
                    ->orWhere('payment_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%');
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
            $payment = BillPayment::scope($publicId)->firstOrFail();
        } else {
            $payment = BillPayment::createNew();
            if (Auth::check() && Auth::user()->account->payment_type_id) {
                $payment->payment_type_id = Auth::user()->account->payment_type_id;
            }
            $payment->created_by = auth::user()->username;
        }

        if ($payment->is_deleted) {
            return $payment;
        }

        if (isset($input['payment_type_id'])) {
            $payment->payment_type_id = $input['payment_type_id'] ? $input['payment_type_id'] : null;
        } else {
            $payment->payment_type_id = PAYMENT_TYPE_CASH;
        }

        if (isset($input['payment_status_id'])) {
            $payment->payment_status_id = $input['payment_status_id'] ? $input['payment_status_id'] : null;
        } else {
            $payment->payment_status_id = PAYMENT_STATUS_COMPLETED;
        }

        if (!isset($input['exchange_currency_id'])) {
            // $vendor = Vendor::scope()->where('id',$vendorId)->first();
            // $payment->exchange_currency_id = ($vendor->currency_id)? $vendor->currency_id: null;
        }
        if (isset($input['payment_date_sql'])) {
            $payment->payment_date = $input['payment_date_sql'];
        } elseif (isset($input['payment_date'])) {
            $payment->payment_date = Utils::toSqlDate($input['payment_date']);
        } else {
            $payment->payment_date = date('Y-m-d');
        }

        $payment->fill($input);

        $paymentTypeId = $input['payment_type_id'];
        if (!$publicId) {
            $vendorId = $input['vendor_id'];
            $amount = round(Utils::parseFloat($input['amount']), 2);
            $amount = min($amount, MAX_INVOICE_AMOUNT);

            if ($paymentTypeId == PAYMENT_TYPE_CREDIT) {
                $credits = VendorCredit::scope()->where('vendor_id', $vendorId)
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
            $payment->vendor_id = $vendorId;
            $payment->amount = $amount;
        }

        $payment->save();

        return $payment;
    }

    public function delete($payment)
    {
        if ($payment->bill->is_deleted) {
            return false;
        }

        parent::delete($payment);
    }

    public function restore($payment)
    {
        if ($payment->bill->is_deleted) {
            return false;
        }

        parent::restore($payment);
    }
}
