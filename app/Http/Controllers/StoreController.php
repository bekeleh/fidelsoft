<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Libraries\Utils;
use App\Models\Location;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use App\Services\StoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class StoreController extends BaseController
{
    // Stores
    protected $storeRepo;
    protected $storeService;
    protected $entityType = ENTITY_STORE;

    public function __construct(StoreRepository $storeRepo, StoreService $storeService)
    {
        // parent::__construct();

        $this->storeRepo = $storeRepo;
        $this->storeService = $storeService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_STORE,
            'datatable' => new StoreDatatable(),
            'title' => trans('texts.stores'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("stores/{$publicId}/edit");
    }

    public function getDatatable($storePublicId = null)
    {
        $search = Input::get('sSearch');
        $accountId = Auth::user()->account_id;
        return $this->storeService->getDatatable($accountId, $search);
    }

    public function getDatatableLocation($locationPublicId = null)
    {
        return $this->storeService->getDatatableLocation($locationPublicId);
    }

    public function create(StoreRequest $request)
    {
        if ($request->location_id != 0) {
            $location = Location::scope($request->location_id)->firstOrFail();
        } else {
            $location = null;
        }

        $data = [
            'locationPublicId' => Input::old('location') ? Input::old('location') : $request->location_id,
            'store' => null,
            'method' => 'POST',
            'url' => 'stores',
            'title' => trans('texts.new_store'),
            'location' => $location,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('stores.edit', $data);
    }

    public function store(CreateStoreRequest $request)
    {
        $data = $request->input();

        $store = $this->storeService->save($data);

        return redirect()->to("stores/{$store->public_id}/edit")->with('success', trans('texts.created_store'));
    }

    public function edit(StoreRequest $request, $publicId = false, $clone = false)
    {
        $store = $request->entity();
        if ($clone) {
            $store->id = null;
            $store->public_id = null;
            $store->deleted_at = null;
            $method = 'POST';
            $url = 'stores';
        } else {
            $method = 'PUT';
            $url = 'stores/' . $store->public_id;
        }

        $data = [
            'location' => null,
            'store' => $store,
            'entity' => $store,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.store.edit'),
            'locationPublicId' => $store->location ? $store->location->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($store));

        return View::make('stores.edit', $data);
    }

    public function update(UpdateStoreRequest $request)
    {
        $data = $request->input();

        $store = $this->storeService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('stores/%s/clone', $store->public_id))->with('success', trans('texts.clone_store'));
        } else {
            return redirect()->to("stores/{$store->public_id}/edit")->with('success', trans('texts.updated_store'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->storeService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_store', $count);

        return $this->returnBulk(ENTITY_STORE, $action, $ids)->with('message', $message);
    }

    public function cloneStore(StoreRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($store = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'locations' => Location::scope()->withActiveOrSelected($store ? $store->location_id : false)->orderBy('name')->get(),
        ];
    }
}
