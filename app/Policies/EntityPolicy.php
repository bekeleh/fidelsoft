<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EntityPolicy.
 */
class EntityPolicy
{
    use HandlesAuthorization;

    public static function create(User $user, $item)
    {
        if (!static::checkModuleEnabled($user, $item))
            return false;


        $entityType = is_string($item) ? $item : $item->getEntityType();
        return $user->hasPermission('create_' . $entityType);
    }

    public static function edit(User $user, $item)
    {
        if (!static::checkModuleEnabled($user, $item))
            return false;


        $entityType = is_string($item) ? $item : $item->getEntityType();
        return $user->hasPermission('edit_' . $entityType) || $user->owns($item);
    }

    public static function view(User $user, $item)
    {
        if (!static::checkModuleEnabled($user, $item))
            return false;

        $entityType = is_string($item) ? $item : $item->getEntityType();
        return $user->hasPermission('view_' . $entityType) || $user->owns($item);
    }

    public static function viewByOwner(User $user, $ownerUserId)
    {
        return $user->id == $ownerUserId;
    }

    public static function editByOwner(User $user, $ownerUserId)
    {
        return $user->id == $ownerUserId;
    }

    private static function checkModuleEnabled(User $user, $item)
    {
        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->account->isModuleEnabled($entityType);
    }
}
