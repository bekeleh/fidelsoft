<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Libraries\Utils;

/**
 * Class Contact.
 */
class Contact extends EntityModel implements AuthenticatableContract, CanResetPasswordContract
{
    use SoftDeletes;
    use Authenticatable;
    use CanResetPassword;
    use Notifiable;

    protected $guard = 'client';
    protected $dates = ['deleted_at'];


    public function getEntityType()
    {
        return ENTITY_CONTACT;
    }


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'send_invoice',
        'custom_value1',
        'custom_value2',
    ];


    protected $hidden = [
        'remember_token',
        'confirmation_code',
    ];


    public static $fieldFirstName = 'first_name';
    public static $fieldLastName = 'last_name';
    public static $fieldEmail = 'email';
    public static $fieldPhone = 'phone';


    public function account()
    {
        return $this->belongsTo('App\Models\Account');
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
        app('App\Ninja\Mailers\ContactMailer')->sendPasswordReset($this, $token);
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
