<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseRequest;
use App\Http\Requests\WarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Libraries\Utils;
use App\Models\Location;
use App\Ninja\Datatables\WarehouseDatatable;
use App\Ninja\Repositories\WarehouseRepository;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class WarehousesController extends BaseController
{
    protected $warehouseRepo;
    protected $warehouseService;
    protected $entityType = ENTITY_WAREHOUSE;

    public function __construct(WarehouseRepository $warehouseRepo, WarehouseService $warehouseService)
    {
        // parent::__construct();

        $this->warehouseRepo = $warehouseRepo;
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_WAREHOUSE);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_WAREHOUSE,
            'datatable' => new WarehouseDatatable(),
            'title' => trans('texts.warehouses'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("warehouses/{$publicId}/edit");
    }

    public function getDatatable($warehousePublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->warehouseService->getDatatable($accountId, $search);
    }

    public function getDatatableLocation($locationPublicId = null)
    {
        return $this->warehouseService->getDatatableLocation($locationPublicId);
    }

    public function create(WarehouseRequest $request)
    {
        $this->authorize('create', ENTITY_WAREHOUSE);
        if ($request->location_id != 0) {
            $location = Location::scope($request->location_id)->firstOrFail();
        } else {
            $location = null;
        }

        $data = [
            'locationPublicId' => Input::old('location') ? Input::old('location') : $request->location_id,
            'warehouse' => null,
            'method' => 'POST',
            'url' => 'warehouses',
            'title' => trans('texts.new_warehouse'),
            'location' => $location,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('warehouses.edit', $data);
    }

    public function store(CreateWarehouseRequest $request)
    {
        $data = $request->input();

        $warehouse = $this->warehouseService->save($data);

        return redirect()->to("warehouses/{$warehouse->public_id}/edit")->with('success', trans('texts.created_warehouse'));
    }

    public function edit(WarehouseRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_WAREHOUSE);
        $warehouse = $request->entity();
        if ($clone) {
            $warehouse->id = null;
            $warehouse->public_id = null;
            $warehouse->deleted_at = null;
            $method = 'POST';
            $url = 'warehouses';
        } else {
            $method = 'PUT';
            $url = 'warehouses/' . $warehouse->public_id;
        }

        $data = [
            'location' => null,
            'warehouse' => $warehouse,
            'entity' => $warehouse,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_warehouse'),
            'locationPublicId' => $warehouse->location ? $warehouse->location->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($warehouse));

        return View::make('warehouses.edit', $data);
    }

    public function update(UpdateWarehouseRequest $request)
    {
        $data = $request->input();

        $warehouse = $this->warehouseService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('warehouses/%s/clone', $warehouse->public_id))->with('success', trans('texts.clone_warehouse'));
        } else {
            return redirect()->to("warehouses/{$warehouse->public_id}/edit")->with('success', trans('texts.updated_warehouse'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->warehouseService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_warehouse', $count);

        return $this->returnBulk(ENTITY_WAREHOUSE, $action, $ids)->with('message', $message);
    }

    public function cloneWarehouse(WarehouseRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($warehouse = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'locations' => Location::scope()->withActiveOrSelected($warehouse ? $warehouse->location_id : false)->orderBy('name')->get(),
        ];
    }
}
