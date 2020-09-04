<?php

namespace App\Ninja\Repositories;

use App;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\BillInvitation;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ActivityRepository extends BaseRepository
{
    private $model;

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }

    public function create($entity, $activityTypeId, $balanceChange = 0, $paidToDateChange = 0, $altEntity = null, $notes = false)
    {
        if ($entity instanceof Client) {
            $client = $entity;
        } elseif ($entity instanceof Invitation) {
            $client = $entity->invoice->client;
        } else {
            $client = $entity->client;
        }

        // init activity and copy over context
        $activity = self::getBlank($altEntity ?: ($client ?: $entity));
        $activity = Utils::copyContext($activity, $entity);
        $activity = Utils::copyContext($activity, $altEntity);

        $activity->activity_type_id = $activityTypeId;
        $activity->adjustment = $balanceChange;
        $activity->client_id = $client ? $client->id : null;
        $activity->balance = $client ? ($client->balance + $balanceChange) : 0;
        $activity->notes = $notes ?: '';

        $keyField = $entity->getKeyField();
        $activity->$keyField = $entity->id;

        $activity->ip = Request::getClientIp();
        $activity->save();

        if ($client) {
            $client->updateBalances($balanceChange, $paidToDateChange);
        }

        return $activity;
    }

    public function createBill($entity, $activityTypeId, $balanceChange = 0, $paidToDateChange = 0, $altEntity = null, $notes = false)
    {
        if ($entity instanceof Vendor) {
            $vendor = $entity;
        } elseif ($entity instanceof BillInvitation) {
            $vendor = $entity->bill->vendor;
        } else {
            $vendor = $entity->vendor;
        }

        // init activity and copy over context
        $activity = self::getBlank($altEntity ?: ($vendor ?: $entity));
        $activity = Utils::copyContext($activity, $entity);
        $activity = Utils::copyContext($activity, $altEntity);

        $activity->activity_type_id = $activityTypeId;
        $activity->adjustment = $balanceChange;
        $activity->vendor_id = $vendor ? $vendor->id : null;
        $activity->balance = $vendor ? ($vendor->balance + $balanceChange) : 0;
        $activity->notes = $notes ?: '';

        $keyField = $entity->getKeyField();

        if ($keyField == 'contact_id') {
            $activity->vendor_contact_id = $entity->id;
        } else {
            $activity->$keyField = $entity->id;
        }
        $activity->ip = Request::getClientIp();

        $activity->save();

        if ($vendor) {
            $vendor->updateBalances($balanceChange, $paidToDateChange);
        }

        return $activity;
    }

    private function getBlank($entity)
    {
        $activity = new Activity();

        if (Auth::check() && Auth::user()->account_id == $entity->account_id) {
            $activity->user_id = Auth::user()->id;
            $activity->account_id = Auth::user()->account_id;
        } else {
            $activity->user_id = $entity->user_id;
            $activity->account_id = $entity->account_id;
        }

        $activity->is_system = App::runningInConsole();
        $activity->token_id = session('token_id');

        return $activity;
    }

    public function findByClientId($clientId, $filter = null)
    {
        $query = DB::table('activities')
            ->leftJoin('accounts', 'accounts.id', '=', 'activities.account_id')
            ->leftJoin('users', 'users.id', '=', 'activities.user_id')
            ->leftJoin('clients', 'clients.id', '=', 'activities.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'activities.invoice_id')
            ->leftJoin('payments', 'payments.id', '=', 'activities.payment_id')
            ->leftJoin('bill_payments', 'bill_payments.id', '=', 'activities.bill_payment_id')
            ->leftJoin('credits', 'credits.id', '=', 'activities.credit_id')
            ->leftJoin('vendor_credits', 'vendor_credits.id', '=', 'activities.vendor_credit_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'activities.task_id')
            ->leftJoin('expenses', 'expenses.id', '=', 'activities.expense_id')
            ->where('clients.id', $clientId)
            ->where('contacts.is_primary', 1)
            ->whereNull('contacts.deleted_at')
            ->select(
                DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                'activities.id',
                'activities.created_at',
                'activities.contact_id',
                'activities.activity_type_id',
                'activities.balance',
                'activities.adjustment',
                'activities.token_id',
                'activities.notes',
                'activities.ip',
                'activities.is_system',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.email as user_email',
                'invoices.invoice_number as invoice_number',
                'invoices.invoice_number as invoice',
                'invoices.public_id as invoice_public_id',
                'invoices.is_recurring',
                'clients.name as client_name',
                'clients.public_id as client_public_id',
                'contacts.id as contact',
                'contacts.first_name as first_name',
                'contacts.last_name as last_name',
                'contacts.email as email',
                'payments.transaction_reference as payment',
                'payments.amount as payment_amount',
                'credits.amount as credit',
                'tasks.description as task_description',
                'tasks.public_id as task_public_id',
                'expenses.public_notes as expense_public_notes',
                'expenses.public_id as expense_public_id'
            );

        return $query;
    }

    public function findByVendorId($vendorId, $filter = null)
    {
        $query = DB::table('activities')
            ->leftJoin('accounts', 'accounts.id', '=', 'activities.account_id')
            ->leftJoin('users', 'users.id', '=', 'activities.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'activities.vendor_id')
            ->leftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->leftJoin('bills', 'bills.id', '=', 'activities.invoice_id')
            ->leftJoin('bill_payments', 'bill_payments.id', '=', 'activities.bill_payment_id')
            ->leftJoin('vendor_credits', 'vendor_credits.id', '=', 'activities.vendor_credit_id')
            ->leftJoin('expenses', 'expenses.id', '=', 'activities.expense_id')
            ->where('vendors.id', $vendorId)
            ->where('vendor_contacts.is_primary', 1)
            ->whereNull('vendor_contacts.deleted_at')
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'activities.id',
                'activities.created_at',
                'activities.contact_id',
                'activities.activity_type_id',
                'activities.balance',
                'activities.adjustment',
                'activities.token_id',
                'activities.notes',
                'activities.ip',
                'activities.is_system',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.email as user_email',
                'bills.bill_number',
                'bills.bill_number as invoice',
                'bills.public_id as invoice_public_id',
                'bills.is_recurring',
                'vendors.name as vendor_name',
                'vendors.public_id as vendor_public_id',
                'vendor_contacts.id as contact',
                'vendor_contacts.first_name as first_name',
                'vendor_contacts.last_name as last_name',
                'vendor_contacts.email as email',
                'bill_payments.transaction_reference as payment',
                'bill_payments.amount as payment_amount',
                'vendor_credits.amount as credit',
                'expenses.public_notes as expense_public_notes',
                'expenses.public_id as expense_public_id'
            );

        return $query;
    }

}
