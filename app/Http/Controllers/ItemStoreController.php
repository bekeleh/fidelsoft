<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Libraries\Utils;
use App\Models\Product;
use App\Models\Store;
use App\Ninja\Datatables\ItemStoreDatatable;
use App\Ninja\Repositories\StoreRepository;
use App\Services\ItemStoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Redirect;

class ItemStoreController extends BaseController
{
    // Stores
    protected $itemStoreRepo;
    protected $itemStoreService;
    protected $entityType = ENTITY_ITEM_STORE;

    public function __construct(StoreRepository $itemStoreRepo, ItemStoreService $itemStoreService)
    {
        // parent::__construct();

        $this->itemStoreRepo = $itemStoreRepo;
        $this->itemStoreService = $itemStoreService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_STORE,
            'datatable' => new ItemStoreDatatable(),
            'title' => trans('texts.item_stores'),
        ]);
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

    public function cloneItemStore(ItemStoreRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function edit(ItemStoreRequest $request, $publicId = false, $clone = false)
    {
        $itemStore = $request->entity();
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
            'title' => trans('texts.item_store.edit'),
            'productPublicId' => $itemStore->product ? $itemStore->product->public_id : null,
            'storePublicId' => $itemStore->store ? $itemStore->store->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemStore));

        return View::make('item_stores.edit', $data);
    }

    /**
     * @param ItemStoreRequest $request
     * @return RedirectResponse
     */
    public function store(ItemStoreRequest $request)
    {
        $data = $request->input();
        $productId = $data['product_id'] = Product::getPrivateId($data['product_id']);
        $storeId = $data['store_id'] = Store::getPrivateId($data['store_id']);
        $validator = $this->validator($data, $productId, $storeId);
        if ($validator->fails()) {
            Session::flash('message', trans('This product already had been registered in the given store.'));
            return redirect()->to("item_stores/create");
        }
        $itemStore = $this->itemStoreService->save($data);

        Session::flash('message', trans('texts.created_item_store'));

        return redirect()->to("item_stores/{$itemStore->public_id}/edit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ItemStoreRequest $request
     * @return Response
     */
    public function update(ItemStoreRequest $request)
    {
        $data = $request->input();

        $itemStore = $this->itemStoreService->save($data, $request->entity());

        Session::flash('message', trans('texts.updated_item_store'));

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_stores/%s/clone', $itemStore->public_id));
        } else {
            return redirect()->to("item_stores/{$itemStore->public_id}/edit");
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemStoreService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_store', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_ITEM_STORE, $action, $ids);
    }

    private static function getViewModel($itemStore = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected($itemStore ? $itemStore->product_id : false)->orderBy('name')->get(),
            'stores' => Store::scope()->withActiveOrSelected($itemStore ? $itemStore->store_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_stores/{$publicId}/edit");
    }

    /**
     * @param $data
     * @param $productId
     * @param $storeId
     * @return mixed
     */
    public function validator($data, $productId, $storeId)
    {
        return Validator::make($data, [
                'product_id' => [
                    'required', 'numeric',
                    Rule::unique('item_stores')
                        ->where(function ($query) use ($productId, $storeId) {
                            return $query->where('product_id', $productId)
                                ->where('store_id', $storeId);
                        }),
                ],
            ]
        );
    }
}
