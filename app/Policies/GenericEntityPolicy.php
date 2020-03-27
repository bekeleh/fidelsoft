<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;
use App\Libraries\Utils;

/**
 * Class GenericEntityPolicy.
 */
abstract class GenericEntityPolicy
{
    use HandlesAuthorization;

//    public static function editByOwner(User $user, $entityType, $ownerUserId)
//    {
//        $className = static::className($entityType);
//        if (method_exists($className, 'editByOwner')) {
//            return call_user_func([$className, 'editByOwner'], $user, $ownerUserId);
//        }
//
//        return false;
//    }

//    public static function viewByOwner(User $user, $entityType, $ownerUserId)
//    {
//        $className = static::className($entityType);
//        if (method_exists($className, 'viewByOwner')) {
//            return call_user_func([$className, 'viewByOwner'], $user, $ownerUserId);
//        }
//
//        return false;
//    }

//    public static function create(User $user, $entityType)
//    {
//
//        if ($user->hasPermission('create_' . $entityType))
//            return true;
//        else
//            return false;
//    }

//    public static function view(User $user, $entityType)
//    {
//
//        if ($user->hasPermission('view_' . $entityType))
//            return true;
//        else
//            return false;
//    }


//    public static function edit(User $user, $item)
//    {
//        if (!static::checkModuleEnabled($user, $item))
//            return false;
//
//
//        $entityType = is_string($item) ? $item : $item->getEntityType();
//        return $user->hasPermission('edit_' . $entityType) || $user->owns($item);
//    }

    abstract protected function tableName();

    public function before(User $user, $ability, $item)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        if ($user->hasAccess('admin')) {
            return true;
        }
    }

    public function index(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.view');
    }

    public function view(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.view');
    }

    public function create(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.create');
    }

    public function update(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.edit');
    }

    public function bulkUpdate(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.bulkUpdate');
    }

    public function delete(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.delete');
    }

    public function bulkDelete(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.bulkDelete');
    }

    public function forceDelete(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.forceDelete');
    }

    public function restore(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.restore');
    }

    public function bulkRestore(User $user, $item = null)
    {
        if (!static::checkModuleEnabled($user, $item)) {
            return false;
        }
        return $user->hasAccess($this->tableName() . '.bulkRestore');
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
