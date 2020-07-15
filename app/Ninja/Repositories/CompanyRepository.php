<?php

namespace App\Ninja\Repositories;


use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class CompanyRepository extends BaseRepository
{
    private $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Company';
    }

    public function all()
    {
        return Company::scope()
        ->withTrashed()
        ->where('is_deleted', '=', false)->get();
    }


    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('companies')
        ->whereNull('deleted_at')
        ->select(
            'companies.id',
            'companies.public_id',
            'companies.plan',
            'companies.plan_term',
            'companies.plan_started',
            'companies.plan_paid',
            'companies.plan_expires',
            'companies.trial_started',
            'companies.trial_plan',
            'companies.pending_plan',
            'companies.pending_term',
            'companies.plan_price',
            'companies.pending_plan_price',
            'companies.num_users',
            'companies.pending_num_users',
            'companies.utm_source',
            'companies.utm_medium',
            'companies.utm_campaign',
            'companies.utm_term',
            'companies.utm_content',
            'companies.discount',
            'companies.discount_expires',
            'companies.promo_expires',
            'companies.bluevine_status',
            'companies.referral_code',
            'companies.created_at',
            'companies.updated_at',
            'companies.deleted_at',
            'companies.created_by',
            'companies.updated_by',
            'companies.deleted_by'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('companies.plan', 'like', '%' . $filter . '%')
                ->orWhere('companies.plan_term', 'like', '%' . $filter . '%')
                ->orWhere('companies.referral_code', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_COMPANY);


        return $query;
    }

    public function save($data, $company = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($company) {
            $company->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $company = Company::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $company = Company::createNew();
            $company->created_by = Auth::user()->username;
        }

        $company->fill($data);

        $company->save();

        return $company;
    }

    public function findPhonetically($companyName)
    {
        $companyNameMeta = metaphone($companyName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $companyId = 0;
        $companies = Company::scope()->get();
        if (!empty($companies)) {
            foreach ($companies as $company) {
                if (!$company->plan) {
                    continue;
                }
                $map[$company->id] = $company;
                $similar = similar_text($companyNameMeta, metaphone($company->plan), $percent);
                if ($percent > $max) {
                    $companyId = $company->id;
                    $max = $percent;
                }
            }
        }

        return ($companyId && isset($map[$companyId])) ? $map[$companyId] : null;
    }
}