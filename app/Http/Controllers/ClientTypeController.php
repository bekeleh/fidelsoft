<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientTypeRequest;
use App\Http\Requests\ClientTypeRequest;
use App\Http\Requests\UpdateClientTypeRequest;
use App\Libraries\Utils;
use App\Ninja\Datatables\ClientTypeDatatable;
use App\Ninja\Repositories\ClientTypeRepository;
use App\Services\ClientTypeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ClientTypeController.
 */
class ClientTypeController extends BaseController
{

    protected $clientTypeRepo;
    protected $clientTypeService;
    protected $entityType = ENTITY_CLIENT_TYPE;

    public function __construct(ClientTypeRepository $clientTypeRepo, ClientTypeService $clientTypeService)
    {
        // parent::__construct();

        $this->clientTypeRepo = $clientTypeRepo;
        $this->clientTypeService = $clientTypeService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_CLIENT_TYPE,
            'datatable' => new ClientTypeDatatable(),
            'title' => trans('texts.client_types'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("client_types/{$publicId}/edit");
    }

    public function getDatatable($clientTypePublicId = null)
    {
        return $this->clientTypeService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(ClientTypeRequest $request)
    {
        $data = [
            'clientType' => null,
            'method' => 'POST',
            'url' => 'client_types',
            'title' => trans('texts.new_store'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('client_types.edit', $data);
    }

    public function cloneClientType(ClientTypeRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function edit(ClientTypeRequest $request, $publicId = false, $clone = false)
    {
        $clientType = $request->entity();
        if ($clone) {
            $clientType->id = null;
            $clientType->public_id = null;
            $clientType->deleted_at = null;
            $method = 'POST';
            $url = 'client_types';
        } else {
            $method = 'PUT';
            $url = 'client_types/' . $clientType->public_id;
        }

        $data = [
            'clientType' => $clientType,
            'entity' => $clientType,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_client_type'),
        ];

        $data = array_merge($data, self::getViewModel($clientType));

        return View::make('client_types.edit', $data);
    }

    public function store(CreateClientTypeRequest $request)
    {
        $data = $request->input();

        $clientType = $this->clientTypeService->save($data);

        return redirect()->to("client_types/{$clientType->public_id}/edit")->with('success', trans('texts.created_client_type'));
    }

    public function update(UpdateClientTypeRequest $request)
    {
        $data = $request->input();

        $clientType = $this->clientTypeService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('client_types/%s/clone', $clientType->public_id))->with('success', trans('texts.clone_client_type'));
        } else {
            return redirect()->to("client_types/{$clientType->public_id}/edit")->with('success', trans('texts.updated_client_type'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->clientTypeService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_client_type', $count);

        return $this->returnBulk(ENTITY_CLIENT_TYPE, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($clientType = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }

}
