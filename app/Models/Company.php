<?php

namespace App\Models;

use App\Libraries\Utils;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;
use Log;

/**
 * Class Company.
 *
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property string|null $plan
 * @property string|null $plan_term
 * @property string|null $plan_started
 * @property string|null $plan_paid
 * @property string|null $plan_expires
 * @property string|null $trial_started
 * @property string|null $trial_plan
 * @property string|null $pending_plan
 * @property string|null $pending_term
 * @property float|null $plan_price
 * @property float|null $pending_plan_price
 * @property int $num_users
 * @property int $pending_num_users
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property float $discount
 * @property \Illuminate\Support\Carbon|null $discount_expires
 * @property \Illuminate\Support\Carbon|null $promo_expires
 * @property string|null $bluevine_status
 * @property string|null $referral_code
 * @property int|null $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereBluevineStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDiscountExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereNumUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePendingNumUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePendingPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePendingPlanPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePendingTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlanExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlanPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlanPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlanStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePlanTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePromoExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTrialPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTrialStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUtmSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUtmTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Company withTrashed()
 * @method static Builder|Company withoutTrashed()
 * @mixin Eloquent
 */
class Company extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\CompanyPresenter';


    protected $fillable = [
        'plan',
        'plan_term',
        'plan_price',
        'plan_paid',
        'plan_started',
        'plan_expires',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    protected $dates = [
        'deleted_at',
        'promo_expires',
        'discount_expires',
    ];


    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function getEntityType()
    {
        return ENTITY_COMPANY;
    }

    public function getRoute()
    {
        return "/companies/{$this->public_id}";
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }

    public function hasActivePromo()
    {
        if ($this->discount_expires) {
            return false;
        }

        return $this->promo_expires && $this->promo_expires->gte(Carbon::today());
    }

    // handle promos and discounts
    public function hasActiveDiscount(Carbon $date = null)
    {
        if (!$this->discount || !$this->discount_expires) {
            return false;
        }

        $date = $date ?: Carbon::today();

        if ($this->plan_term == PLAN_TERM_MONTHLY) {
            return $this->discount_expires->gt($date);
        } else {
            return $this->discount_expires->subMonths(11)->gt($date);
        }
    }

    public function discountedPrice($price)
    {
        if (!$this->hasActivePromo() && !$this->hasActiveDiscount()) {
            return $price;
        }

        return $price - ($price * $this->discount);
    }

    public function daysUntilPlanExpires()
    {
        if (!$this->hasActivePlan()) {
            return 0;
        }

        return Carbon::parse($this->plan_expires)->diffInDays(Carbon::today());
    }

    public function hasActivePlan()
    {
        return Carbon::parse($this->plan_expires) >= Carbon::today();
    }

    public function hasExpiredPlan($plan)
    {
        if ($this->plan != $plan) {
            return false;
        }

        return Carbon::parse($this->plan_expires) < Carbon::today();
    }

    public function hasEarnedPromo()
    {
        if (!Utils::isNinjaProd() || Utils::isPro()) {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already had a discount or a promotion is active return false
        if ($this->discount_expires || $this->hasActivePromo()) {
            return false;
        }

        $discounts = [
            52 => [.6, 3],
            16 => [.4, 3],
            10 => [.25, 5],
        ];

        foreach ($discounts as $weeks => $promo) {
            list($discount, $validFor) = $promo;
            $difference = $this->created_at->diffInWeeks();
            if ($difference >= $weeks && $discount > $this->discount) {
                $this->discount = $discount;
                $this->promo_expires = date_create()->modify($validFor . ' days')->format('Y-m-d');
                $this->save();

                return true;
            }
        }

        return false;
    }

    public function getPlanDetails($includeInactive = false, $includeTrial = true)
    {
        $account = $this->accounts()->first();

        if (!$account) {
            return false;
        }

        return $account->getPlanDetails($includeInactive, $includeTrial);
    }

    public function processRefund($user)
    {
        if (!$this->payment) {
            return false;
        }

        $account = $this->accounts()->first();
        $planDetails = $account->getPlanDetails(false, false);

        if (!empty($planDetails['started'])) {
            $deadline = clone $planDetails['started'];
            $deadline->modify('+30 days');

            if ($deadline >= date_create()) {
                $accountRepo = app('App\Ninja\Repositories\AccountRepository');
                $ninjaAccount = $accountRepo->getNinjaAccount();
                $paymentDriver = $ninjaAccount->paymentDriver();
                $paymentDriver->refundPayment($this->payment);

                Log::info("Refunded Plan Payment: {$account->name} - {$user->email} - Deadline: {$deadline->format('Y-m-d')}");

                return true;
            }
        }

        return false;
    }

    public function applyDiscount($amount)
    {
        $this->discount = $amount;
        $this->promo_expires = date_create()->modify('3 days')->format('Y-m-d');
    }

    public function applyFreeYear()
    {
        if ($this->plan_started && $this->plan_started != '0000-00-00') {
            return;
        }

        $this->plan = PLAN_PRO;
        $this->plan_term = PLAN_TERM_YEARLY;
        $this->plan_price = PLAN_PRICE_PRO_MONTHLY;
        $this->plan_started = date_create()->format('Y-m-d');
        $this->plan_paid = date_create()->format('Y-m-d');
        $this->plan_expires = date_create()->modify('1 year')->format('Y-m-d');
    }
}

Company::deleted(function ($company) {
    if (!env('MULTI_DB_ENABLED')) {
        return;
    }

    $server = DbServer::whereName(config('database.default'))->firstOrFail();

    LookupCompany::deleteWhere([
        'company_id' => $company->id,
        'db_server_id' => $server->id,
    ]);
});
