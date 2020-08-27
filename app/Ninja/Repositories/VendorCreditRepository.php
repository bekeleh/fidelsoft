<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\VendorCredit;
use Datatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorCreditRepository extends BaseRepository
{
    private $model;

    public function __construct(Vendor $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\VendorCredit';
    }

    public function find($vendorPublicId = false, $filter = null)
    {
        $query = DB::table('vendor_credits')
            ->leftJoin('accounts', 'accounts.id', '=', 'vendor_credits.account_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'vendor_credits.vendor_id')
            ->leftJoin('vendor_contacts', 'vendor_contacts.vendor_id', '=', 'vendors.id')
            ->where('vendors.account_id', Auth::user()->account_id)
            ->where('vendor_contacts.is_primary', true)
            // ->whereNull('vendor_contacts.deleted_at')
            // ->whereNull('vendors.deleted_at')
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendor_credits.public_id',
                DB::raw("COALESCE(NULLIF(vendors.name,''), NULLIF(CONCAT(vendor_contacts.first_name, ' ', vendor_contacts.last_name),''), NULLIF(vendor_contacts.email,'')) vendor_name"),
                'vendors.public_id as vendor_public_id',
                'vendors.user_id as vendor_user_id',
                'vendor_credits.amount',
                'vendor_credits.balance',
                'vendor_credits.credit_date as credit_date_sql',
                DB::raw("CONCAT(vendor_credits.credit_date, vendor_credits.created_at) as credit_date"),
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'vendor_credits.private_notes',
                'vendor_credits.public_notes',
                'vendor_credits.deleted_at',
                'vendor_credits.is_deleted',
                'vendor_credits.user_id',
                'vendor_credits.created_at',
                'vendor_credits.updated_at',
                'vendor_credits.deleted_at',
                'vendor_credits.created_by',
                'vendor_credits.updated_by',
                'vendor_credits.deleted_by'
            );

        if ($vendorPublicId) {
            $query->where('vendors.public_id', $vendorPublicId);
        }

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendors.name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.phone', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_VENDOR_CREDIT);

        return $query;
    }

    public function getVendorDatatable($vendorId)
    {
        $query = DB::table('vendor_credits')
            ->leftJoin('accounts', 'accounts.id', '=', 'vendor_credits.account_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'vendor_credits.vendor_id')
            ->where('vendor_credits.vendor_id', $vendorId)
            ->where('vendors.deleted_at', null)
            ->where('vendor_credits.deleted_at', null)
            ->select(
                DB::raw('COALESCE(vendors.currency_id, accounts.currency_id) currency_id'),
                DB::raw('COALESCE(vendors.country_id, accounts.country_id) country_id'),
                'vendor_credits.amount',
                'vendor_credits.balance',
                'vendor_credits.credit_date',
                'vendor_credits.public_notes'
            );

        $table = Datatable::query($query)
            ->addColumn('credit_date', function ($model) {
                return Utils::fromSqlDate($model->credit_date);
            })
            ->addColumn('amount', function ($model) {
                return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
            })
            ->addColumn('balance', function ($model) {
                return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
            })
            ->addColumn('public_notes', function ($model) {
                return e($model->public_notes);
            })
            ->make();

        return $table;
    }

    public function save($input, $credit = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($credit) {
            $credit->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $credit = VendorCredit::scope($publicId)->firstOrFail();
        } else {
            $credit = VendorCredit::createNew();
            $credit->balance = Utils::parseFloat($input['amount']);
            $credit->vendor_id = Vendor::getPrivateId($input['vendor_id']);
            $credit->credit_date = date('Y-m-d');
            $credit->created_by = auth::user()->username;
        }

        $credit->fill($input);

        if (isset($input['credit_date'])) {
            $credit->credit_date = Utils::toSqlDate($input['credit_date']);
        }
        if (isset($input['amount'])) {
            $credit->amount = Utils::parseFloat($input['amount']);
        }
        if (isset($input['balance'])) {
            $credit->balance = Utils::parseFloat($input['balance']);
        }

        $credit->save();

        return $credit;
    }
}
