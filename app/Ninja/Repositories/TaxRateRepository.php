<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaxRateRepository extends BaseRepository
{
    private $model;

    public function __construct(TaxRate $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\TaxRate';
    }

    public function all()
    {
        return TaxRate::scope()->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('tax_rates')
            ->where('tax_rates.account_id', '=', $accountId)
//            ->where('tax_rates.deleted_at', '=', null)
            ->select(
                'tax_rates.id',
                'tax_rates.public_id',
                'tax_rates.name as tax_rate_name',
                'tax_rates.rate',
                'tax_rates.notes',
                'tax_rates.deleted_at',
                'tax_rates.is_inclusive',
                'tax_rates.is_deleted',
                'tax_rates.created_at',
                'tax_rates.updated_at',
                'tax_rates.deleted_at',
                'tax_rates.created_by',
                'tax_rates.updated_by',
                'tax_rates.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('tax_rates.name', 'like', '%' . $filter . '%')
                    ->orWhere('tax_rates.rate', 'like', '%' . $filter . '%')
                    ->orWhere('tax_rates.is_inclusive', 'like', '%' . $filter . '%')
                    ->orWhere('tax_rates.created_by', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_TAX_RATE);

        return $query;
    }

    public function save($data, $taxRate = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($taxRate) {
            $taxRate->updated_by = Auth::user()->username;
            // do nothing
        } elseif ($publicId) {
            $taxRate = TaxRate::scope($data['public_id'])->firstOrFail();
        } else {
            $taxRate = TaxRate::createNew();
            $taxRate->created_by = Auth::user()->username;
        }

        $taxRate->fill($data);
        $taxRate->name = isset($data['name']) ? trim($data['name']) : '';
        $taxRate->rate = Utils::parseFloat($data['rate']);

        $taxRate->save();

        return $taxRate;
    }

    /*
    public function save($taxRates)
    {
        $taxRateIds = [];

        foreach ($taxRates as $record) {
            if (!isset($record->rate) || (isset($record->is_deleted) && $record->is_deleted)) {
                continue;
            }

            if (!isset($record->name) || !trim($record->name)) {
                continue;
            }

            if ($record->public_id) {
                $taxRate = TaxRate::scope($record->public_id)->firstOrFail();
            } else {
                $taxRate = TaxRate::createNew();
            }

            $taxRate->rate = Utils::parseFloat($record->rate);
            $taxRate->name = trim($record->name);
            $taxRate->save();

            $taxRateIds[] = $taxRate->public_id;
        }

        $taxRates = TaxRate::scope()->get();

        foreach ($taxRates as $taxRate) {
            if (!in_array($taxRate->public_id, $taxRateIds)) {
                $taxRate->delete();
            }
        }
    }
    */
}
