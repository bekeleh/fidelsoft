<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStoreRequest;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\UpdateItemStoreRequest;
use App\Libraries\Utils;
use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Store;
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

    public function getItemList($storePublicId = null)
    {
        $storePublicId = Input::get('store_id');
        $accountId = Auth::user()->account_id;
        $storeId = Store::getPrivateId($storePublicId);

        $data = $this->itemStoreRepo->getItems($accountId, $storeId);

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

    public function getDatatableStore($storePublicId = null)
    {
        return $this->itemStoreService->getDatatableStore($storePublicId);
    }

    public function create(ItemStoreRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_STORE);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }
        if ($request->store_id != 0) {
            $store = Store::scope($request->store_id)->firstOrFail();
        } else {
            $store = null;
        }

        $data = [
            'product' => $product,
            'store' => $store,
            'itemStore' => null,
            'method' => 'POST',
            'url' => 'item_stores',
            'title' => trans('texts.new_item_store'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'storePublicId' => Input::old('store') ? Input::old('store') : $request->store_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_stores.edit', $data);
    }

    public function store(CreateItemStoreRequest $request)
    {
        $data = $request->input();

        $itemStore = $this->itemStoreService->save($data);

        return redirect()->to("item_stores/{$itemStore->public_id}/edit")->with('success', trans('texts.created_item_store'));
    }

    public function edit(ItemStoreRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_STORE);
        $itemStore = ItemStore::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemStore->id = null;
            $itemStore->public_id = null;
            $itemStore->deleted_at = null;
            $method = 'POST';
            $url = 'item_stores';
        } else {
            $method = 'PUT';
            $url = 'item_stores/' . $itemStore->public_id;
        }

        $data = [
            'product' => null,
            'store' => null,
            'itemStore' => $itemStore,
            'entity' => $itemStore,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_store'),
            'productPublicId' => $itemStore->product ? $itemStore->product->public_id : null,
            'storePublicId' => $itemStore->store ? $itemStore->store->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemStore));

        return View::make('item_stores.edit', $data);
    }

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
            'products' => Product::withCategory('itemBrand.itemCategory'),
            'stores' => Store::scope()->withActiveOrSelected($itemStore ? $itemStore->store_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_stores/{$publicId}/edit");
    }
}
