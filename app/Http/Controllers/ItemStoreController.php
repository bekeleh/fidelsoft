<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStoreRequest;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\UpdateItemStoreRequest;
use App\Libraries\Utils;
use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Warehouse;
use App\Ninja\Datatables\ItemStoreDatatable;
use App\Ninja\Repositories\ItemStoreRepository;
use App\Services\ItemStoreService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ItemStoreController.
 */
class ItemStoreController extends BaseController
{
    protected $itemStoreRepo;
    protected $itemStoreService;
    protected $entityType = ENTITY_ITEM_STORE;

    public function __construct(ItemStoreRepository $itemStoreRepo, ItemStoreService $itemStoreService)
    {
        // parent::__construct();

        $this->itemStoreRepo = $itemStoreRepo;
        $this->itemStoreService = $itemStoreService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_ITEM_STORE);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_STORE,
            'datatable' => new ItemStoreDatatable(),
            'title' => trans('texts.item_stores'),
        ]);
    }

    public function getItemList($warehousePublicId = null)
    {
        $warehousePublicId = Input::get('warehouse_id');
        $accountId = Auth::user()->account_id;
        $warehouseId = Warehouse::getPrivateId($warehousePublicId);

        $data = $this->itemStoreRepo->getItems($accountId, $warehouseId);

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function getDatatable($itemStorePublicId = null)
    {
        return $this->itemStoreService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->itemStoreService->getDatatableProduct($productPublicId);
    }

    public function getDatatableWarehouse($warehousePublicId = null)
    {
        return $this->itemStoreService->getDatatableWarehouse($warehousePublicId);
    }

    /**
     * @param ItemStoreRequest $request
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function create(ItemStoreRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_STORE);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }
        if ($request->warehouse_id != 0) {
            $warehouse = Warehouse::scope($request->warehouse_id)->firstOrFail();
        } else {
            $warehouse = null;
        }

        $data = [
            'product' => $product,
            'warehouse' => $warehouse,
            'itemStore' => null,
            'method' => 'POST',
            'url' => 'item_stores',
            'title' => trans('texts.new_item_store'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'warehousePublicId' => Input::old('warehouse') ? Input::old('warehouse') : $request->warehouse_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_stores.edit', $data);
    }

    /**
     * @param CreateItemStoreRequest $request
     * @return RedirectResponse
     */
    public function store(CreateItemStoreRequest $request)
    {
        $data = $request->input();

        $itemStore = $this->itemStoreService->save($data);

        return redirect()->to("item_stores/{$itemStore->public_id}/edit")->with('success', trans('texts.created_item_store'));
    }

    /**
     * @param ItemStoreRequest $request
     * @param bool $publicId
     * @param bool $clone
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function edit(ItemStoreRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_STORE);
        $itemStore = ItemStore::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemStore->id = null;
            $itemStore->public_id = null;
            $itemStore->deleted_at = null;
            $itemStore->qty = 0;
            $method = 'POST';
            $url = 'item_stores';
        } else {
            $method = 'PUT';
            $url = 'item_stores/' . $itemStore->public_id;
        }

        $data = [
            'product' => null,
            'warehouse' => null,
            'itemStore' => $itemStore,
            'entity' => $itemStore,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_store'),
            'productPublicId' => $itemStore->product ? $itemStore->product->public_id : null,
            'warehousePublicId' => $itemStore->warehouse ? $itemStore->warehouse->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemStore));

        return View::make('item_stores.edit', $data);
    }

    /**
     * @param UpdateItemStoreRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateItemStoreRequest $request)
    {
        $data = $request->input();

        $itemStore = $this->itemStoreService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_stores/%s/clone', $itemStore->public_id))->with('success', trans('texts.clone_item_store'));
        } else {
            return redirect()->to("item_stores/{$itemStore->public_id}/edit")->with('success', trans('texts.updated_item_store'));
        }
    }

    /**
     * @param ItemStoreRequest $request
     * @param $publicId
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function cloneItemStore(ItemStoreRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemStoreService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_store', $count);

        return $this->returnBulk(ENTITY_ITEM_STORE, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($itemStore = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected(false)->products()->orderBy('product_key')->get(),
            'warehouses' => Warehouse::scope()->withActiveOrSelected($itemStore ? $itemStore->warehouse_id : false)->orderBy('name')->get(),
        ];
    }

    /**
     * @param $publicId
     * @return RedirectResponse
     */
    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_stores/{$publicId}/edit");
    }
}
