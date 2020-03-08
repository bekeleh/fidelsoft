<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Libraries\Utils;
use App\Models\Location;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Repositories\LocationRepository;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Exception;
use Redirect;

/**
 * Class LocationController.
 */
class LocationController extends BaseController
{
    /**
     * @var LocationService
     */
    protected $locationService;

    /**
     * @var LocationRepository
     */
    protected $locationRepo;

    /**
     * LocationController constructor.
     *
     * @param LocationService $locationService
     * @param LocationRepository $locationRepo
     */
    public function __construct(LocationService $locationService, LocationRepository $locationRepo)
    {
        //parent::__construct();
        $this->locationService = $locationService;
        $this->locationRepo = $locationRepo;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_LOCATION,
            'datatable' => new LocationDatatable(),
            'title' => trans('texts.locations'),
            'statuses' => Location::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("locations/$publicId/edit");
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable()
    {
        return $this->locationService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function cloneLocation(LocationRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    /**
     * @param LocationRequest $request
     * @param $publicId
     *
     * @param bool $clone
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(LocationRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_LOCATION, $request->entity()]);

        $account = Auth::user()->account;
        $location = Location::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $location->id = null;
            $location->public_id = null;
            $location->deleted_at = null;
            $url = 'locations';
            $method = 'POST';
        } else {
            $url = 'locations/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'location' => $location,
            'entity' => $location,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_location'),
        ];

        return View::make('locations.location', $data);
    }

    /**
     * @param LocationRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(LocationRequest $request)
    {

        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'location' => null,
            'method' => 'POST',
            'url' => 'locations',
            'title' => trans('texts.create_location'),
        ];

        return View::make('locations.location', $data);
    }

    /**
     * @param LocationRequest $request
     * @return RedirectResponse
     */
    public function store(LocationRequest $request)
    {
        return $this->save();
    }

    /**
     * @param LocationRequest $request
     * @param $publicId
     *
     * @return RedirectResponse
     */
    public function update(LocationRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $locationPublicId
     *
     * @return RedirectResponse
     */
    private function save($locationPublicId = false)
    {
        if ($locationPublicId) {
            $location = Location::scope($locationPublicId)->withTrashed()->firstOrFail();
            $location->updated_by = auth::user()->username;
        } else {
            $location = Location::createNew();
            $location->created_by = auth::user()->username;
        }
        $this->locationRepo->save(Input::all(), $location);

        $message = $locationPublicId ? trans('texts.updated_location') : trans('texts.created_location');
        Session::flash('message', $message);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'relocation', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('locations/%s/clone', $location->public_id));
        } else {
            return redirect()->to("locations/{$location->public_id}/edit");
        }
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $locations = Location::scope($ids)->get();
            $data = [];
            foreach ($locations as $location) {
                $data[] = $location->location_key;
            }
            return redirect("invoices/create")->with('selectedLocations', $data);
        } else {
            $count = $this->locationService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_location', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_LOCATION, $action, $ids);
    }
}
