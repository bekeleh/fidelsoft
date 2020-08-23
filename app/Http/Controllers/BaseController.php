<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    use DispatchesJobs, AuthorizesRequests;

    protected $entityType;

    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function returnBulk($entityType, $action, $ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $isDatatable = filter_var(request()->datatable, FILTER_VALIDATE_BOOLEAN);
        $referer = Request::server('HTTP_REFERER');
        $entityTypes = Utils::pluralizeEntityType($entityType);
        // when restoring redirect to entity
        if ($action == 'restore' && count($ids) == 1) {
            return redirect("{$entityTypes}/" . $ids[0]);
            // when viewing from a datatable list
        } elseif (strpos($referer, '/clients/') || strpos($referer, '/projects/')) {
            return redirect($referer);
        } elseif ($isDatatable || ($action == 'archive' || $action == 'delete')) {
            return redirect("{$entityTypes}");
            // when viewing individual entity
        } elseif (count($ids)) {
            return redirect("{$entityTypes}/" . $ids[0] . '/edit');
        } else {
            return redirect("{$entityTypes}");
        }
    }

    protected function downloadResponse($filename, $contents, $type = 'application/pdf')
    {
        header('Content-Type: ' . $type);
        header('Content-Length: ' . strlen($contents));

        if (!request()->debug) {
            header('Content-disposition: attachment; filename="' . $filename . '"');
        }

        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        echo $contents;

        exit;
    }

}
