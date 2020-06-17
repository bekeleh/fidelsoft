<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManufacturerRequest;
use App\Http\Requests\ManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;
use App\Libraries\Utils;
use App\Models\Manufacturer;
use App\Ninja\Datatables\ManufacturerDatatable;
use App\Ninja\Repositories\ManufacturerRepository;
use App\Services\ManufacturerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ManufacturerController.
 */
class ManufacturerController extends BaseController
{

    protected $manufacturerService;

    /**
     * @var ManufacturerRepository
     */
    protected $manufacturerRepo;

    /**
     * ManufacturerController constructor.
     *
     * @param ManufacturerService $manufacturerService
     * @param ManufacturerRepository $manufacturerRepo
     */
    public function __construct(ManufacturerService $manufacturerService, ManufacturerRepository $manufacturerRepo)
    {
        //parent::__construct();
        $this->manufacturerService = $manufacturerService;
        $this->manufacturerRepo = $manufacturerRepo;
    }

    public function index()
    {
        $this->authorize('view', auth::user(), $this->entityType);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_MANUFACTURER,
            'datatable' => new ManufacturerDatatable(),
            'title' => trans('texts.manufacturers'),
            'statuses' => Manufacturer::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("manufacturers/$publicId/edit");
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->manufacturerService->getDatatable($accountId, $search);
    }

    public function create(ManufacturerRequest $request)
    {
        Auth::user()->can('create', [ENTITY_MANUFACTURER, $request->entity()]);
        $data = [
            'manufacturer' => null,
            'method' => 'POST',
            'url' => 'manufacturers',
            'title' => trans('texts.create_manufacturer'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('manufacturers.edit', $data);
    }

    public function store(CreateManufacturerRequest $request)
    {
        $data = $request->input();

        $manufacturer = $this->manufacturerService->save($data);

        return redirect()->to("manufacturers/{$manufacturer->public_id}/edit")->with('success', trans('texts.created_manufacturer'));
    }

    public function edit(ManufacturerRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('edit', [ENTITY_MANUFACTURER, $request->entity()]);

        $manufacturer = Manufacturer::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $manufacturer->id = null;
            $manufacturer->public_id = null;
            $manufacturer->deleted_at = null;
            $method = 'POST';
            $url = 'manufacturers';
        } else {
            $method = 'PUT';
            $url = 'manufacturers/' . $manufacturer->public_id;
        }

        $data = [
            'manufacturer' => $manufacturer,
            'entity' => $manufacturer,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_manufacturer'),
        ];

        $data = array_merge($data, self::getViewModel($manufacturer));

        return View::make('manufacturers.edit', $data);
    }

    public function update(UpdateManufacturerRequest $request, $publicId)
    {
        $data = $request->input();
        $manufacturer = $this->manufacturerService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('manufacturers/%s/clone', $manufacturer->public_id))->with('success', trans('texts.clone_manufacturer'));
        } else {
            return redirect()->to("manufacturers/{$manufacturer->public_id}/edit")->with('success', trans('texts.updated_manufacturer'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $manufacturers = Manufacturer::scope($ids)->get();
            $data = [];
            foreach ($manufacturers as $manufacturer) {
                $data[] = $manufacturer->manufacturer_key;
            }
            return redirect("invoices/create")->with('manufacturers', $data);
        } else {
            $count = $this->manufacturerService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_manufacturer', $count);

        return $this->returnBulk(ENTITY_MANUFACTURER, $action, $ids)->with('success', $message);
    }

    public function cloneManufacturer(ManufacturerRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($manufacturer = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
