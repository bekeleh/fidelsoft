<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\ActivityDatatable;
use App\Ninja\Repositories\ActivityRepository;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class ActivityController extends BaseController
{
    protected $activityService;
    protected $activityRepo;

    public function __construct(ActivityService $activityService, ActivityRepository $activityRepo)
    {
        //parent::__construct();

        $this->activityService = $activityService;
        $this->activityRepo = $activityRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_ACTIVITY);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ACTIVITY,
            'datatable' => new ActivityDatatable(),
            'title' => trans('texts.activities'),
        ]);
    }

//  client activity
    public function getDatatable($clientPublicId)
    {
        $search = Input::get('sSearch');

        return $this->activityService->getDatatable($clientPublicId, $search);
    }

//  vendor activity
    public function getVendorDatatable($vendorPublicId)
    {
        $search = Input::get('sSearch');

        return $this->activityService->getVendorDatatable($vendorPublicId, $search);
    }
}
