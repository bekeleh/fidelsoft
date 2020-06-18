<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EntityPolicy.
 */
abstract class EntityPolicy
{
    use HandlesAuthorization;

    abstract protected function getEntity();

    /**
     * @param User $user
     * @param $entity
     * @return bool
     */

    public static function create(User $user, $entity)
    {
        if (!static::checkModuleEnabled($user, $entity)) {
            return false;
        }

        $entityType = is_string($entity) ? $entity : $entity->getEntityType();

        return $user->hasPermission('create_' . $entityType);
    }

    /**
     * @param User $user
     * @param $entity - entity name or object
     *
     * @return bool
     */

    public static function edit(User $user, $entity)
    {
        if (!static::checkModuleEnabled($user, $entity)) {
            return false;
        }

        $entityType = is_string($entity) ? $entity : $entity->getEntityType();


        return $user->hasPermission('edit_' . $entityType) || $user->owns($entity);
    }

    /**
     * @param User $user
     * @param $entity - entity name or object
     *
     * @return bool
     */

    public static function view(User $user, $entity)
    {
        if (!static::checkModuleEnabled($user, $entity)) {
            return false;
        }

        $entityType = is_string($entity) ? $entity : $entity->getEntityType();

        return $user->hasPermission('view_' . $entityType) || $user->owns($entity);
    }

    /**
     * @param User $user
     * @param $ownerUserId
     *
     * Legacy permissions - retaining these for legacy code however new code
     *                      should use auth()->user()->can('view', $ENTITY_TYPE)
     *
     * $ENTITY_TYPE can be either the constant ie ENTITY_INVOICE, or the entity $object
     *
     * @return bool
     */

    public static function viewByOwner(User $user, $ownerUserId)
    {
        return $user->id == $ownerUserId;
    }

    /**
     * @param User $user
     * @param $ownerUserId
     *
     * Legacy permissions - retaining these for legacy code however new code
     *                      should use auth()->user()->can('edit', $ENTITY_TYPE)
     *
     * $ENTITY_TYPE can be either the constant ie ENTITY_INVOICE, or the entity $object
     *
     * @return bool
     */

    public static function editByOwner(User $user, $ownerUserId)
    {
        return $user->id == $ownerUserId;
    }

    /**
     * @param User $user
     * @param $entity - entity name or object
     * @return bool
     */

    private static function checkModuleEnabled(User $user, $entity)
    {
        $entityType = is_string($entity) ? $entity : $entity->getEntityType();

        return $user->account->isModuleEnabled($entityType);
    }
}
