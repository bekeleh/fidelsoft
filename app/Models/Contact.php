<?php

namespace App\Models;

use App\Models\EntityModel;
use App\Libraries\Utils;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class Contact.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string|null $contact_key
 * @property string|null $bot_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_primary
 * @property int $send_invoice
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $last_login
 * @property string|null $banned_until
 * @property int|null $confirmation_code
 * @property int|null $remember_token
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read mixed $link
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static Builder|Contact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereBannedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereBotUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereContactKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereSendInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Contact withTrashed()
 * @method static Builder|Contact withoutTrashed()
 * @mixin Eloquent
 */
class Contact extends EntityModel implements AuthenticatableContract, CanResetPasswordContract
{
    use SoftDeletes;
    use Authenticatable;
    use CanResetPassword;
    use Notifiable;

    protected $table = 'contacts';
    protected $guard = 'client';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'send_invoice',
        'custom_value1',
        'custom_value2',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    protected $hidden = [
        'password',
        'confirmation_code',
        'remember_token',
        'confirmation_code',
    ];


    public static $fieldFirstName = 'first_name';
    public static $fieldLastName = 'last_name';
    public static $fieldEmail = 'email';
    public static $fieldPhone = 'phone';

    public function getEntityType()
    {
        return ENTITY_CONTACT;
    }

    public function getRoute()
    {
        return "/contacts/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function getPersonType()
    {
        return PERSON_CONTACT;
    }

    public function getName()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        } else {
            return $this->email;
        }
    }

    public function getSearchName()
    {
        $name = $this->getFullName();
        $email = $this->email;

        if ($name && $email) {
            return sprintf('%s <%s>', $name, $email);
        } else {
            return $name ?: $email;
        }
    }

    public function getContactKeyAttribute($contact_key)
    {
        if (empty($contact_key) && $this->id) {
            $this->contact_key = $contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            static::where('id', $this->id)->update(['contact_key' => $contact_key]);
        }

        return $contact_key;
    }

    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        } else {
            return '';
        }
    }

    public function getLinkAttribute()
    {
        if (!$this->account) {
            $this->load('account');
        }

        $account = $this->account;
        $iframe_url = $account->iframe_url;
        $url = trim(SITE_URL, '/');

        if ($account->hasFeature(FEATURE_CUSTOM_URL)) {
            if (Utils::isNinjaProd() && !Utils::isReseller()) {
                $url = $account->present()->clientPortalLink();
            }

            if ($iframe_url) {
                if ($account->is_custom_domain) {
                    $url = $iframe_url;
                } else {
                    return "{$iframe_url}?{$this->contact_key}/client";
                }
            } elseif ($this->account->subdomain) {
                $url = Utils::replaceSubdomain($url, $account->subdomain);
            }
        }

        return "{$url}/client/dashboard/{$this->contact_key}";
    }

    public function sendPasswordResetNotification($token)
    {
        //$this->notify(new ResetPasswordNotification($token));
        app('App\Ninja\Mailers\ClientMailer')->sendPasswordReset($this, $token);
    }
}

Contact::creating(function ($contact) {
    LookupContact::createNew($contact->account->account_key, [
        'contact_key' => $contact->contact_key,
    ]);
});

Contact::deleted(function ($contact) {
    if ($contact->forceDeleting) {
        LookupContact::deleteWhere([
            'contact_key' => $contact->contact_key,
        ]);
    }
});
