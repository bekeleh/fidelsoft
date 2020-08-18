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
    protected $itemWarehouseRepo;
    protected $itemWarehouseService;
    protected $entityType = ENTITY_ITEM_STORE;

    public function __construct(ItemStoreRepository $itemWarehouseRepo, ItemStoreService $itemWarehouseService)
    {
        // parent::__construct();

        $this->itemWarehouseRepo = $itemWarehouseRepo;
        $this->itemWarehouseService = $itemWarehouseService;
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

        $data = $this->itemWarehouseRepo->getItems($accountId, $warehouseId);

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function getDatatable($itemWarehousePublicId = null)
    {
        return $this->itemWarehouseService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->itemWarehouseService->getDatatableProduct($productPublicId);
    }

    public function getDatatableWarehouse($warehousePublicId = null)
    {
        return $this->itemWarehouseService->getDatatableWarehouse($warehousePublicId);
    }

    public function create(ItemStoreRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_STORE);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }
        if ($request->warehouse_id != 0) {
            $store = Warehouse::scope($request->warehouse_id)->firstOrFail();
        } else {
            $store = null;
        }

        $data = [
            'product' => $product,
            'store' => $store,
            'itemWarehouse' => null,
            'method' => 'POST',
            'url' => 'item_stores',
            'title' => trans('texts.new_item_store'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'storePublicId' => Input::old('store') ? Input::old('store') : $request->warehouse_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_stores.edit', $data);
    }

    public function store(CreateItemStoreRequest $request)
    {
        $data = $request->input();

        $itemWarehouse = $this->itemWarehouseService->save($data);

        return redirect()->to("item_stores/{$itemWarehouse->public_id}/edit")->with('success', trans('texts.created_item_store'));
    }

    public function edit(ItemStoreRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_STORE);
        $itemWarehouse = ItemStore::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemWarehouse->id = null;
            $itemWarehouse->public_id = null;
            $itemWarehouse->deleted_at = null;
            $method = 'POST';
            $url = 'item_stores';
        } else {
            $method = 'PUT';
            $url = 'item_stores/' . $itemWarehouse->public_id;
        }

        $data = [
            'product' => null,
            'store' => null,
            'itemWarehouse' => $itemWarehouse,
            'entity' => $itemWarehouse,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_store'),
            'productPublicId' => $itemWarehouse->product ? $itemWarehouse->product->public_id : null,
            'storePublicId' => $itemWarehouse->store ? $itemWarehouse->store->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemWarehouse));

        return View::make('item_stores.edit', $data);
    }

    public function update(UpdateItemStoreRequest $request)
    {
        $data = $request->input();

        $itemWarehouse = $this->itemWarehouseService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_stores/%s/clone', $itemWarehouse->public_id))->with('success', trans('texts.clone_item_store'));
        } else {
            return redirect()->to("item_stores/{$itemWarehouse->public_id}/edit")->with('success', trans('texts.updated_item_store'));
        }
    }

    public function cloneItemStore(ItemStoreRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemWarehouseService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_store', $count);

        return $this->returnBulk(ENTITY_ITEM_STORE, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($itemWarehouse = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected(false)->products()->orderBy('product_key')->get(),
            'warehouses' => Warehouse::scope()->withActiveOrSelected($itemWarehouse ? $itemWarehouse->warehouse_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_stores/{$publicId}/edit");
    }
}
