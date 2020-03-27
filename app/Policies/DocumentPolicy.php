<?php

namespace App\Policies;

/**
 * Class DocumentPolicy.
 */
class DocumentPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'documents';
    }
}
