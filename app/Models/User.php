<?php

namespace App\Models;

use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Libraries\Utils;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class User.
 */
class User extends EntityModel implements AuthenticatableContract, CanResetPasswordContract
{
    use PresentableTrait;
    use SoftDeletes;
    use Notifiable;
    use Authenticatable, Authorizable, CanResetPassword;

    protected $presenter = 'App\Ninja\Presenters\UserPresenter';

    private $slack_webhook_url;

    public static $all_permissions = [
//        'create_all' => 0b0001,
//        'view_all' => 0b0010,
//        'edit_all' => 0b0100,
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'manager_id',
        'user_id',
        'store_id',
        'location_id',
        'first_name',
        'last_name',
        'phone',
        'username',
        'email',
        'password',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    protected $hidden = [
        'password',
        'reset_password_code',
        'permissions',
        'remember_token',
        'confirmation_code',
        'persist_code',
        'oauth_user_id',
        'oauth_provider_id',
        'google_2fa_secret',
        'google_2fa_phone',
        'remember_2fa_token',
        'slack_webhook_url',
    ];

    public function getEntityType()
    {
        return ENTITY_USER;
    }

    public function getRoute()
    {
        return "/users/{$this->public_id}";
    }

    public function decodePermissions()
    {
        return json_decode($this->permissions, true);
    }

    public function showMap()
    {
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
    }

    public function hasAddress($location = false)
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        return false;
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store');
    }

    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }

//    public function getName()
//    {
//        return $this->getDisplayName();
//    }
//
//    public function getPersonType()
//    {
//        return PERSON_USER;
//    }

    public function getReminderEmail()
    {
        return $this->email;
    }

    public function isPro()
    {
        return $this->account->isPro();
    }

    public function isEnterprise()
    {
        return $this->account->isEnterprise();
    }

    public function isTrusted()
    {
        if (Utils::isSelfHost()) {
            true;
        }

        return $this->account->isPro() && !$this->account->isTrial();
    }

    public function hasActivePromo()
    {
        return $this->account->hasActivePromo();
    }

    public function hasFeature($feature)
    {
        return $this->account->hasFeature($feature);
    }

    public function isTrial()
    {
        return $this->account->isTrial();
    }

    public function maxInvoiceDesignId()
    {
        return $this->hasFeature(FEATURE_MORE_INVOICE_DESIGNS) ? 13 : COUNT_FREE_DESIGNS;
    }


    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        } elseif ($this->email) {
            return $this->email;
        } else {
            return trans('texts.guest');
        }
    }

    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        } else {
            return '';
        }
    }

    public function showGreyBackground()
    {
        return !$this->theme_id || in_array($this->theme_id, [2, 3, 5, 6, 7, 8, 10, 11, 12]);
    }


    public function getRequestsCount()
    {
        return Session::get(SESSION_COUNTER, 0);
    }

    public function afterSave($success = true, $forced = false)
    {
        if ($this->email) {
            return parent::afterSave($success = true, $forced = false);
        } else {
            return true;
        }
    }


    public function getMaxNumClients()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_CLIENTS_PRO;
        }

        if ($this->id < LEGACY_CUTOFF) {
            return MAX_NUM_CLIENTS_LEGACY;
        }

        return MAX_NUM_CLIENTS;
    }

    public function getMaxNumVendors()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_VENDORS_PRO;
        }

        return MAX_NUM_VENDORS;
    }

    public function clearSession()
    {
        $keys = [
            SESSION_USER_ACCOUNTS,
            SESSION_TIMEZONE,
            SESSION_DATE_FORMAT,
            SESSION_DATE_PICKER_FORMAT,
            SESSION_DATETIME_FORMAT,
            SESSION_CURRENCY,
            SESSION_LOCALE,
        ];

        foreach ($keys as $key) {
            Session::forget($key);
        }
    }

    public static function onUpdatingUser($user)
    {
        if ($user->password != $user->getOriginal('password')) {
            $user->failed_logins = 0;
        }

        // if the user changes their email then they need to reconfirm it
        if ($user->isEmailBeingChanged()) {
            $user->confirmed = 0;
            $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));
        }
    }

    public static function onUpdatedUser($user)
    {
        if (!$user->getOriginal('email')
            || $user->getOriginal('email') == TEST_USERNAME
            || $user->getOriginal('email') == 'tests@bitrock.com') {
            event(new UserSignedUp());
        }

        event(new UserSettingsChanged($user));
    }


    public function isEmailBeingChanged()
    {
        return Utils::isNinjaProd() && $this->email != $this->getOriginal('email');
    }


    /**
     * Checks to see if the user has the required permission.
     *
     * @param mixed $permission Either a single permission or an array of possible permissions
     * @param mixed $requireAll - True to require all permissions, false to require only one
     *
     * @return bool
     */

    public function hasPermission($permission, $requireAll = false)
    {
        if ($this->is_admin) {
            return true;
        } elseif (is_string($permission)) {
            if (is_array(json_decode($this->permissions, 1)) && in_array($permission, json_decode($this->permissions, 1))) {
                return true;
            } else {
                // Loop through the permission to see if any of them granted this via groups.
                return $this->hasPermission($permission);
            }
        } elseif (is_array($permission)) {
            if ($requireAll)
                return count(array_intersect($permission, json_decode($this->permissions, 1))) == count($permission);
            else
                return count(array_intersect($permission, json_decode($this->permissions, 1))) > 0;

        }

        return false;
    }

    public function isSuperUser()
    {
        $userPermissions = json_decode($this->permissions, true);
        if (!$userPermissions) {
            return false;
        }

        foreach ($this->groups as $userGroup) {
            $group_permissions = json_decode($userGroup->permissions, true);
            $group_array = (array)$group_permissions;
            if ((array_key_exists('superuser', $group_array)) && ($group_permissions['superuser'] == '1')) {
                return true;
            }
        }
//        note: Fidel ERP master account
        if ((array_key_exists('superuser', $userPermissions)) && ($userPermissions['superuser'] == '1')) {
            return true;
        }

        return false;
    }

    public function isAdminUser()
    {
        $userPermissions = json_decode($this->permissions, true);
        if (!$userPermissions) {
            return false;
        }

        foreach ($this->groups as $userGroup) {
            $group_permissions = json_decode($userGroup->permissions, true);
            $group_array = (array)$group_permissions;
            if ((array_key_exists('admin', $group_array)) && ($group_permissions['admin'] == '1')) {
                return true;
            }
        }
//        note: companies master account
        if ((array_key_exists('admin', $userPermissions)) && ($userPermissions['admin'] == '1')) {
            return true;
        }

        return false;
    }

    public function groups()
    {
        return $this->belongsToMany('\App\Models\PermissionGroup', 'users_groups', 'user_id', 'group_id');
    }

    public function accountStatus()
    {
        if ($this->throttle) {
            if ($this->throttle->suspended === 1) {
                return 'suspended';
            } elseif ($this->throttle->banned === 1) {
                return 'banned';
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function viewModel($model, $entityType)
    {
        //todo permissions
        if ($this->hasPermission('view_' . $entityType))
            return true;
        elseif ($model->user_id == $this->id)
            return true;
        else
            return false;
    }

    public function throttle()
    {
        return $this->hasOne('\App\Models\Throttle');
    }

    public function owns($entity)
    {
        return !empty($entity->user_id) && $entity->user_id == $this->id;
    }


    public function filterId()
    {
        //todo permissions
        return $this->hasPermission('view_all') ? false : $this->id;
    }

    public function filterIdByEntity($entity)
    {
        //todo permissions
        return $this->hasPermission('view_' . $entity) ? false : $this->id;
    }

    public function canAddUsers()
    {
        if (!Utils::isNinjaProd()) {
            return true;
        } elseif (!$this->hasFeature(FEATURE_USERS)) {
            return false;
        }

        $account = $this->account;
        $company = $account->company;

        $numUsers = 1;
        foreach ($company->accounts as $account) {
            $numUsers += $account->users->count() - 1;
        }

        return $numUsers < $company->num_users;
    }

    public function canCreateOrEdit($entityType, $entity = false)
    {
        return ($entity && $this->can('edit', $entity))
            || (!$entity && $this->can('create', $entityType));
    }

    public function primaryAccount()
    {
        return $this->account->company->accounts->sortBy('id')->first();
    }

    public function sendPasswordResetNotification($token)
    {
        //$this->notify(new ResetPasswordNotification($token));
        app('App\Ninja\Mailers\UserMailer')->sendPasswordReset($this, $token);
    }

    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
    }

    public function hasAcceptedLatestTerms()
    {
        if (!NINJA_TERMS_VERSION) {
            return true;
        }

        return $this->accepted_terms_version == NINJA_TERMS_VERSION;
    }

    public function acceptLatestTerms($ip)
    {
        $this->accepted_terms_version = NINJA_TERMS_VERSION;
        $this->accepted_terms_timestamp = date('Y-m-d H:i:s');
        $this->accepted_terms_ip = $ip;

        return $this;
    }

    public function ownsEntity($entity)
    {
        return $entity->user_id == $this->id;
    }

    public function shouldNotify($invoice)
    {
        if (!$this->email || !$this->confirmed) {
            return false;
        }

        if ($this->cannot('view', $invoice)) {
            return false;
        }

        if ($this->only_notify_owned && !$this->ownsEntity($invoice)) {
            return false;
        }

        return true;
    }

    public function permissionsMap()
    {
        $data = [];
        $permissions = json_decode($this->permissions);

        if (!$permissions) {
            return $data;
        }

        $keys = array_values((array)$permissions);
        $values = array_fill(0, count($keys), true);

        return array_combine($keys, $values);
    }
}

User::created(function ($user) {
    LookupUser::createNew($user->account->account_key, [
        'email' => $user->email,
        'user_id' => $user->id,
        'confirmation_code' => $user->confirmation_code,
    ]);
});

User::updating(function ($user) {
    User::onUpdatingUser($user);

    $dirty = $user->getDirty();
    if (array_key_exists('email', $dirty)
        || array_key_exists('confirmation_code', $dirty)
        || array_key_exists('oauth_user_id', $dirty)
        || array_key_exists('oauth_provider_id', $dirty)
        || array_key_exists('referral_code', $dirty)) {
        LookupUser::updateUser($user->account->account_key, $user);
    }
});

User::updated(function ($user) {
    User::onUpdatedUser($user);
});

User::deleted(function ($user) {
    if (!$user->email) {
        return;
    }

    if ($user->forceDeleting) {
        LookupUser::deleteWhere([
            'email' => $user->email
        ]);
    }
});
