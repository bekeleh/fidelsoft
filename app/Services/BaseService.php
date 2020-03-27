<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class BaseService.
 */
class BaseService
{
    use DispatchesJobs;

    protected function getRepo()
    {
        return null;
    }

    public function bulk($ids, $action)
    {
        if (!$ids) {
            return 0;
        }

        $entities = $this->getRepo()->findByPublicIdsWithTrashed($ids);

        if ($entities) {
            foreach ($entities as $entity) {
                if (Auth::user()->can('edit', $entity)) {
                    $this->getRepo()->$action($entity);
                }
            }
        }
        return count($entities);
    }
}
