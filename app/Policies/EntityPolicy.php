<?php

namespace App\Policies;

use App\Libraries\Utils;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

/**
 * Class EntityPolicy.
 */
abstract class EntityPolicy
{
    use HandlesAuthorization;

    abstract protected function tableName();

    public function before(User $user, $ability, $item)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        if ($user->hasAccess('admin')) {
            return true;
        }
    }

    public function settings(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.settings');
    }

    public function index(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.index');
    }

    public function view(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.view');
    }

    public function create(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.create');
    }

    public function store(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.store');
    }

    public function edit(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.edit');
    }

    public function update(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.edit');
    }

    public function delete(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }

        return $user->hasAccess($this->tableName() . '.delete');
    }

    public function forceDelete(User $user, $item = null)
    {
        if ($item) {
            if (!static::checkModuleEnabled($user, $item)) {
                return false;
            }
        }
        return $user->hasAccess($this->tableName() . '.forceDelete');
    }

    private static function checkModuleEnabled(User $user, $item)
    {
        $entityType = is_string($item) ? $item : $item->getEntityType();
        return $user->account->isModuleEnabled($entityType);
    }

    private static function className($entityType)
    {
        if (!Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Policies\\{$module->getName()}Policy";
            }
        }

        $studly = Str::studly($entityType);

        return "App\\Policies\\{$studly}Policy";
    }
}
