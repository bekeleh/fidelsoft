<?php

namespace App\Policies;

/**
 * Class DocumentPolicy.
 */
class DocumentPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_DOCUMENT;
    }
}
