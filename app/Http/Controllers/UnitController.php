<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Libraries\Utils;
use App\Ninja\Datatables\UnitDatatable;
use App\Ninja\Repositories\UnitRepository;
use App\Services\UnitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class UnitController extends BaseController
{
    // Stores
    protected $unitRepo;
    protected $unitService;
    protected $entityType = ENTITY_UNIT;

    public function __construct(UnitRepository $unitRepo, UnitService $unitService)
    {
        // parent::__construct();

        $this->unitRepo = $unitRepo;
        $this->unitService = $unitService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_UNIT,
            'datatable' => new UnitDatatable(),
            'title' => trans('texts.units'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("units/{$publicId}/edit");
    }

    public function getDatatable($unitPublicId = null)
    {
        return $this->unitService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(UnitRequest $request)
    {
        $data = [
            'unit' => null,
            'method' => 'POST',
            'url' => 'units',
            'title' => trans('texts.new_unit'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('units.edit', $data);
    }

    public function edit(UnitRequest $request, $publicId = false, $clone = false)
    {
        $unit = $request->entity();
        if ($clone) {
            $unit->id = null;
            $unit->public_id = null;
            $unit->deleted_at = null;
            $method = 'POST';
            $url = 'units';
        } else {
            $method = 'PUT';
            $url = 'units/' . $unit->public_id;
        }

        $data = [
            'unit' => $unit,
            'entity' => $unit,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.unit.edit'),
        ];

        $data = array_merge($data, self::getViewModel($unit));

        return View::make('units.edit', $data);
    }

    public function update(UnitRequest $request)
    {
        $data = $request->input();

        $unit = $this->unitService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('units/%s/clone', $unit->public_id))->with('message', trans('texts.clone_unit'));
        } else {
            return redirect()->to("units/{$unit->public_id}/edit")->with('message', trans('texts.updated_unit'));
        }
    }

    public function store(UnitRequest $request)
    {
        $data = $request->input();

        $unit = $this->unitService->save($data);

        return redirect()->to("units/{$unit->public_id}/edit")->with('message', trans('texts.created_unit'));
    }

    public function bulk()
    {
        $action = Input::get('action');

        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->unitService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_unit', $count);

        return $this->returnBulk(ENTITY_UNIT, $action, $ids)->with('message', $message);
    }

    public function cloneUnit(UnitRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($unit = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
