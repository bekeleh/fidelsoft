<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


/**
 * Class EntityModel.
 */
class EntityModel extends Eloquent
{

    public $timestamps = true;
    protected static $hasPublicId = true;
    protected $hidden = ['id'];
    public static $notifySubscriptions = true;

    public static $statuses = [
        STATUS_ACTIVE,
        STATUS_ARCHIVED,
        STATUS_DELETED,
    ];


    public static function createNew($context = null)
    {
        $className = get_called_class();
        $entity = new $className();

        if ($context) {
            $user = $context instanceof User ? $context : $context->user;
            $account = $context->account;
        } elseif (Auth::check()) {
            $user = Auth::user();
            $account = Auth::user()->account;
        } else {
            Utils::fatalError();
        }

        $entity->user_id = $user->id;
        $entity->account_id = $account->id;

        // store references to the original user/account to prevent needing to reload them
        $entity->setRelation('user', $user);
        $entity->setRelation('account', $account);

        if (static::$hasPublicId) {
            $entity->public_id = static::getNextPublicId($entity->account_id);
        }

        return $entity;
    }

    private static function getNextPublicId($accountId)
    {
        $className = get_called_class();

        if (method_exists($className, 'trashed')) {
            $lastEntity = $className::whereAccountId($accountId)->withTrashed();
        } else {
            $lastEntity = $className::whereAccountId($accountId);
        }

        $lastEntity = $lastEntity->orderBy('public_id', 'DESC')->first();

        if ($lastEntity) {
            return $lastEntity->public_id + 1;
        } else {
            return 1;
        }
    }

    public static function withCategory($relatedName)
    {
        if (!$relatedName) {
            return null;
        }
        $className = get_called_class();
        if ($relatedName != '') {
            $query = $className::scope()->with($relatedName)->orderBy('name')->get();

            return self::getNameWithCategory($query, $relatedName);
        }
        return null;
    }

    public static function getNameWithCategory($query, $relatedName)
    {
        foreach ($query as $subQuery) {
            $name_str = '';
            if ($subQuery->name != '') {
                if ($subQuery->$relatedName->name != '') {
                    $name_str .= e($subQuery->name) . ' (' . e($subQuery->$relatedName->name) . ')';
                } else {
                    $name_str .= e($subQuery->name);
                }
            }
            $subQuery->name = $name_str;
        }

        return ($query);

    }


    public static function getPrivateId($publicId)
    {
        if (!$publicId) {
            return null;
        }

        $className = get_called_class();

        if (method_exists($className, 'trashed')) {
            return $className::scope($publicId)->withTrashed()->value('id');
        } else {
            return $className::scope($publicId)->value('id');
        }
    }

    public static function getAccountId($context = null)
    {
        $className = get_called_class();
        $entity = new $className();

        if ($context) {
            $user = $context instanceof User ? $context : $context->user;
            $account = $context->account;
        } elseif (Auth::check()) {
            $user = Auth::user();
            $account = Auth::user()->account;
        } else {
            Utils::fatalError();
        }

        $entity->user_id = $user->id;
        $entity->account_id = $account->id;

        if (static::$hasPublicId) {
            $entity->public_id = static::getNextPublicId($entity->account_id);
        }

        return $entity->account_id;

    }

    public function getActivityKey()
    {
        return '[' . $this->getEntityType() . ':' . $this->public_id . ':' . $this->getDisplayName() . ']';
    }

    public function entityKey()
    {
        return $this->public_id . ':' . $this->getEntityType();
    }

    public function subEntityType()
    {
        return $this->getEntityType();
    }

    public function isEntityType($type)
    {
        return $this->getEntityType() === $type;
    }

    /*
    public function getEntityType()
    {
        return '';
    }

    public function getNmae()
    {
        return '';
    }
    */


    public function scopeScope($query, $publicId = false, $accountId = false)
    {
        // If 'false' is passed as the publicId return nothing rather than everything
        if (func_num_args() > 1 && !$publicId && !$accountId) {
            $query->where('id', '=', 0);
            return $query;
        }

        if (!$accountId) {
            $accountId = Auth::user()->account_id;
        }

        $query->where($this->getTable() . '.account_id', '=', $accountId);

        if ($publicId) {
            if (is_array($publicId)) {
                $query->whereIn('public_id', $publicId);
            } else {
                $query->wherePublicId($publicId);
            }
        }

        if (Auth::check() && method_exists($this, 'getEntityType') && !Auth::user()->hasPermission('view_' . $this->getEntityType()) && $this->getEntityType() != ENTITY_TAX_RATE && $this->getEntityType() != ENTITY_DOCUMENT) {
            $query->where(Utils::pluralizeEntityType($this->getEntityType()) . '.user_id', '=', Auth::user()->id);
        }

        return $query;
    }

    public function scopeWithActiveOrSelected($query, $id = false)
    {
        return $query->withTrashed()->where(function ($query) use ($id) {
            $query->whereNull('deleted_at')->orWhere('id', '=', $id);
        });
    }

    public function scopeWithArchived($query)
    {
        return $query->withTrashed()->where('is_deleted', '=', false);
    }


    public function getName()
    {
        return $this->public_id;
    }


    public function getDisplayName()
    {
        return $this->getName();
    }


    public static function getClassName($entityType)
    {
        if (!Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Models\\{$module->getName()}";
            }
        }

        if ($entityType == ENTITY_QUOTE || $entityType == ENTITY_RECURRING_INVOICE) {
            $entityType = ENTITY_INVOICE;
        }

        return 'App\\Models\\' . ucwords(Utils::toCamelCase($entityType));
    }

    public static function trueFalseFormatter($value)
    {
        if (($value) && (($value == 'true') || ($value == '1'))) {
            return '<i class="fa fa-check text-success"></i>';
        } else {
            return '<i class="fa fa-times text-danger"></i>';
        }
    }

    public static function getStatusClass($primaryValue, $secondaryValue)
    {
        if (!empty($primaryValue) && !empty($secondaryValue)) {
            if (floatval($primaryValue) > floatval($secondaryValue)) {
//                return 'default';
                return 'success';
            } else {
                return 'danger';
            }
        } elseif (!empty($primaryValue)) {
            return 'primary';
        } else {
            return 'warning';
        }
    }


    public static function getTransformerName($entityType)
    {
        if (!Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Transformers\\{$module->getName()}Transformer";
            }
        }

        return 'App\\Ninja\\Transformers\\' . ucwords(Utils::toCamelCase($entityType)) . 'Transformer';
    }

    public function setNullValues()
    {
        foreach ($this->fillable as $field) {
            if (strstr($field, '_id') && !$this->$field) {
                $this->$field = null;
            }
        }
    }


    public function getKeyField()
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $name = $parts[count($parts) - 1];

        return strtolower($name) . '_id';
    }

    public static function getValidator($data, $entityType = false, $entity = false)
    {

    }


    public static function validate($data, $entityType = false, $entity = false)
    {
        if (!$entityType) {
            $className = get_called_class();
            $entityBlank = new $className();
            $entityType = $entityBlank->getEntityType();
        }

        // Use the API request if it exists
        $action = $entity ? 'update' : 'create';
        $requestClass = sprintf('App\\Http\\Requests\\%s%sAPIRequest', ucwords($action), Str::studly($entityType));
        if (!class_exists($requestClass)) {
            $requestClass = sprintf('App\\Http\\Requests\\%s%sRequest', ucwords($action), Str::studly($entityType));
        }

        $request = new $requestClass();
        $request->setUserResolver(function () {
            return Auth::user();
        });
        $request->setEntity($entity);
        $request->replace($data);

        if (!$request->authorize()) {
            return trans('texts.not_allowed');
        }

        $validator = Validator::make($data, $request->rules());

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return true;
        }
    }

    public static function getIcon($entityType)
    {
        $icons = [
            'dashboard' => 'tachometer',
            'clients' => 'users',
            'vendors' => 'users',
            'products' => 'cube',
            'item_stores' => 'cubes',
            'item_movements' => 'exchange',
            'stores' => 'building',
            'sale_types' => 'files-o',
            'item_prices' => 'money',
            'locations' => 'location-arrow',
            'invoices' => 'file-pdf-o',
            'payments' => 'credit-card',
            'recurring_invoices' => 'files-o',
            'recurring_expenses' => 'files-o',
            'credits' => 'credit-card',
            'quotes' => 'file-text-o',
            'proposals' => 'th-large',
            'tasks' => 'clock-o',
            'expenses' => 'file-image-o',
            'settings' => 'cog',
            'self-update' => 'download',
            'reports' => 'th-list',
            'projects' => 'briefcase',
        ];

        return array_get($icons, $entityType);
    }

    public function loadFromRequest()
    {
        foreach (static::$requestFields as $field) {
            if ($value = request()->$field) {
                $this->$field = strpos($field, 'date') ? Utils::fromSqlDate($value) : $value;
            }
        }
    }

// isDirty return true if the field's new value is the same as the old one
    public function isChanged()
    {
        foreach ($this->fillable as $field) {
            if ($this->$field != $this->getOriginal($field)) {
                return true;
            }
        }

        return false;
    }

    public static function getFormUrl($entityType)
    {
        if (in_array($entityType, [ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_TEMPLATE])) {
            return str_replace('_', 's/', Utils::pluralizeEntityType($entityType));
        } else {
            return Utils::pluralizeEntityType($entityType);
        }
    }

    public static function getStates($entityType = false)
    {
        $data = [];

        foreach (static::$statuses as $status) {
            $data[$status] = trans("texts.{$status}");
        }

        return $data;
    }

    public static function getStatuses($entityType = false)
    {
        return [];
    }

    public static function getStatesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStates($entityType);
    }

    public static function getStatusesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStatuses($entityType);
    }

    public function statusClass()
    {
        return '';
    }

    public function statusLabel()
    {
        return '';
    }

    public function save(array $options = [])
    {
        try {
            return parent::save($options);
        } catch (\Illuminate\Database\QueryException $exception) {
            // check if public_id has been taken
            if ($exception->getCode() == 23000 && static::$hasPublicId) {
                $nextId = static::getNextPublicId($this->account_id);
                if ($nextId != $this->public_id) {
                    $this->public_id = $nextId;
                    if (env('MULTI_DB_ENABLED')) {
                        if ($this->contact_key) {
                            $this->contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                        } elseif ($this->invitation_key) {
                            $this->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                        }
                    }
                    return $this->save($options);
                }
            }
            throw $exception;
        }
    }

    public function equalTo($obj)
    {
        if (empty($obj->id)) {
            return false;
        }

        return $this->id == $obj->id && $this->getEntityType() == $obj->entityType;
    }

    public function __call($method, $params)
    {
        if (count(config('modules.relations'))) {
            $entityType = $this->getEntityType();

            if ($entityType) {
                $config = implode('.', ['modules.relations.' . $entityType, $method]);
                if (config()->has($config)) {
                    $function = config()->get($config);
                    return $function($this);
                }
            }
        }

        return parent::__call($method, $params);
    }
}
