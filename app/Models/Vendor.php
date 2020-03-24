<?php

namespace App\Models;

use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use App\Libraries\Utils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model Vendor.
 */
class Vendor extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;


    protected $presenter = 'App\Ninja\Presenters\VendorPresenter';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'currency_id',
        'website',
        'transaction_name',
        'custom_value1',
        'custom_value2',
    ];


    public static $fieldName = 'name';
    public static $fieldPhone = 'work_phone';
    public static $fieldAddress1 = 'address1';
    public static $fieldAddress2 = 'address2';
    public static $fieldCity = 'city';
    public static $fieldState = 'state';
    public static $fieldPostalCode = 'postal_code';
    public static $fieldNotes = 'notes';
    public static $fieldCountry = 'country';


    public static function getImportColumns()
    {
        return [
            self::$fieldName,
            self::$fieldPhone,
            self::$fieldAddress1,
            self::$fieldAddress2,
            self::$fieldCity,
            self::$fieldState,
            self::$fieldPostalCode,
            self::$fieldCountry,
            self::$fieldNotes,
            'contact_first_name',
            'contact_last_name',
            'contact_email',
            'contact_phone',
        ];
    }


    public static function getImportMap()
    {
        return [
            'first' => 'contact_first_name',
            'last' => 'contact_last_name',
            'email' => 'contact_email',
            'mobile|phone' => 'contact_phone',
            'work|office' => 'work_phone',
            'name|organization|vendor' => 'name',
            'street2|address2' => 'address2',
            'street|address|address1' => 'address1',
            'city' => 'city',
            'state|province' => 'state',
            'zip|postal|code' => 'postal_code',
            'country' => 'country',
            'note' => 'notes',
        ];
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function vendor_contacts()
    {
        return $this->hasMany('App\Models\VendorContact');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function size()
    {
        return $this->belongsTo('App\Models\Size');
    }

    public function industry()
    {
        return $this->belongsTo('App\Models\Industry');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense', 'vendor_id', 'id');
    }

    public function addVendorContact($data, $isPrimary = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        if (!$this->wasRecentlyCreated && $publicId && intval($publicId) > 0) {
            $contact = VendorContact::scope($publicId)->whereVendorId($this->id)->firstOrFail();
        } else {
            $contact = VendorContact::createNew();
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->vendor_contacts()->save($contact);
    }

    public function getRoute()
    {
        return "/vendors/{$this->public_id}";
    }


    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function getEntityType()
    {
        return 'vendor';
    }

    public function showMap()
    {
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
    }

    public function hasAddress()
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($this->$field) {
                return true;
            }
        }

        return false;
    }

    public function getDateCreated()
    {
        if ($this->created_at == '0000-00-00 00:00:00') {
            return '---';
        } else {
            return $this->created_at->format('m/d/y h:i a');
        }
    }

    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->currency_id ?: DEFAULT_CURRENCY;
    }

    public function getTotalExpenses()
    {
        return DB::table('expenses')
            ->select('expense_currency_id', DB::raw('sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)) as amount'))
            ->whereVendorId($this->id)
            ->whereIsDeleted(false)
            ->groupBy('expense_currency_id')
            ->get();
    }
}

Vendor::creating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::created(function ($vendor) {
    event(new VendorWasCreated($vendor));
});

Vendor::updating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::updated(function ($vendor) {
    event(new VendorWasUpdated($vendor));
});

Vendor::deleting(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::deleted(function ($vendor) {
    event(new VendorWasDeleted($vendor));
});
