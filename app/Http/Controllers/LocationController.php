<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Libraries\Utils;
use App\Models\Location;
use App\Ninja\Datatables\LocationDatatable;
use App\Ninja\Repositories\LocationRepository;
use App\Services\LocationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class LocationController.
 */
class LocationController extends BaseController
{

    protected $locationService;

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

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->locationService->getDatatable($accountId, $search);
    }

    public function create(LocationRequest $request)
    {
        Auth::user()->can('create', [ENTITY_LOCATION, $request->entity()]);
        $data = [
            'location' => null,
            'method' => 'POST',
            'url' => 'locations',
            'title' => trans('texts.create_location'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('locations.edit', $data);
    }

    public function store(CreateLocationRequest $request)
    {
        $data = $request->input();

        $location = $this->locationService->save($data);

        return redirect()->to("locations/{$location->public_id}/edit")->with('success', trans('texts.created_location'));
    }

    public function edit(LocationRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('edit', [ENTITY_LOCATION, $request->entity()]);

        $location = Location::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $location->id = null;
            $location->public_id = null;
            $location->deleted_at = null;
            $method = 'POST';
            $url = 'locations';
        } else {
            $method = 'PUT';
            $url = 'locations/' . $location->public_id;
        }

        $data = [
            'location' => $location,
            'entity' => $location,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_location'),
        ];

        $data = array_merge($data, self::getViewModel($location));

        return View::make('locations.edit', $data);
    }

    public function update(UpdateLocationRequest $request, $publicId)
    {
        $data = $request->input();
        $location = $this->locationService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('locations/%s/clone', $location->public_id))->with('success', trans('texts.clone_location'));
        } else {
            return redirect()->to("locations/{$location->public_id}/edit")->with('success', trans('texts.updated_location'));
        }
    }

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
            return redirect("invoices/create")->with('locations', $data);
        } else {
            $count = $this->locationService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_location', $count);

        return $this->returnBulk(ENTITY_LOCATION, $action, $ids)->with('success', $message);
    }

    public function cloneLocation(LocationRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($location = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
