<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Libraries\Utils;
use App\Models\Location;
use App\Models\Store;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use App\Services\StoreService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class StoreController.
 */
class StoreController extends BaseController
{
    /**
     * @var StoreService
     */
    protected $storeService;

    /**
     * @var StoreRepository
     */
    protected $storeRepo;

    /**
     * StoreController constructor.
     *
     * @param StoreService $storeService
     * @param StoreRepository $storeRepo
     */
    public function __construct(StoreService $storeService, StoreRepository $storeRepo)
    {
        //parent::__construct();
        $this->storeService = $storeService;
        $this->storeRepo = $storeRepo;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_STORE,
            'datatable' => new StoreDatatable(),
            'title' => trans('texts.stores'),
            'statuses' => Store::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("stores/$publicId/edit");
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable()
    {
        return $this->storeService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function cloneStore(StoreRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    /**
     * @param StoreRequest $request
     * @param $publicId
     *
     * @param bool $clone
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(StoreRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_STORE, $request->entity()]);

        $account = Auth::user()->account;
        $store = Store::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $store->id = null;
            $store->public_id = null;
            $store->deleted_at = null;
            $url = 'stores';
            $method = 'POST';
        } else {
            $url = 'stores/' . $publicId;
            $method = 'PUT';
        }
        $data = [
            'locationPublicId' => $store->location ? $store->location->public_id : null,
            'account' => $account,
            'store' => $store,
            'location' => null,
            'entity' => $store,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_store'),
        ];
        $data = array_merge($data, self::getViewModel($store));
//        dd($data);
        return View::make('stores.edit', $data);
    }

    /**
     * @param StoreRequest $request
     * @return \Illuminate\Contracts\View\View
     */
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
            'location' => $location,
            'method' => 'POST',
            'url' => 'stores',
            'title' => trans('texts.create_store'),
        ];
        $data = array_merge($data, self::getViewModel());
        return View::make('stores.edit', $data);
    }

    /**
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        dd($request->all());
        return $this->save();
    }

    /**
     * @param StoreRequest $request
     * @param $publicId
     *
     * @return RedirectResponse
     */
    public function update(StoreRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $storePublicId
     *
     * @return RedirectResponse
     */
    private function save($storePublicId = false)
    {
        if ($storePublicId) {
            $store = Store::scope($storePublicId)->withTrashed()->firstOrFail();
            $store->updated_by = auth::user()->username;
        } else {
            $store = Store::createNew();
            $store->created_by = auth::user()->username;
        }
        dd(Input::all());
        $this->storeRepo->save(Input::all(), $store);

        $message = $storePublicId ? trans('texts.updated_store') : trans('texts.created_store');
        Session::flash('message', $message);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('stores/%s/clone', $store->public_id));
        } else {
            return redirect()->to("stores/{$store->public_id}/edit");
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
            $stores = Store::scope($ids)->get();
            $data = [];
            foreach ($stores as $store) {
                $data[] = $store->store_key;
            }
            return redirect("invoices/create")->with('selectedStores', $data);
        } else {
            $count = $this->storeService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_store', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_STORE, $action, $ids);
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
