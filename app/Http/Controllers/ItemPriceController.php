<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemPriceRequest;
use App\Http\Requests\ItemPriceRequest;
use App\Http\Requests\UpdateItemPriceRequest;
use App\Libraries\Utils;
use App\Models\Product;
use App\Models\SaleType;
use App\Ninja\Datatables\ItemPriceDatatable;
use App\Ninja\Repositories\ItemPriceRepository;
use App\Services\ItemPriceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class ItemPriceController extends BaseController
{
    protected $itemPriceRepo;
    protected $itemPriceService;
    protected $entityType = ENTITY_ITEM_PRICE;

    public function __construct(ItemPriceRepository $itemPriceRepo, ItemPriceService $itemPriceService)
    {
        // parent::__construct();

        $this->itemPriceRepo = $itemPriceRepo;
        $this->itemPriceService = $itemPriceService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_ITEM_PRICE);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_PRICE,
            'datatable' => new ItemPriceDatatable(),
            'title' => trans('texts.item_price'),
        ]);
    }

    public function getDatatable($itemPricePublicId = null)
    {
        return $this->itemPriceService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->itemPriceService->getDatatableProduct($productPublicId);
    }

    public function getDatatableSaleType($saleTypeTypePublicId = null)
    {
        return $this->itemPriceService->getDatatableSaleType($saleTypeTypePublicId);
    }

    public function create(ItemPriceRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_PRICE);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }
        if ($request->sale_type_id != 0) {
            $saleType = SaleType::scope($request->sale_type_id)->firstOrFail();
        } else {
            $saleType = null;
        }

        $data = [
            'product' => $product,
            'saleType' => $saleType,
            'itemPrice' => null,
            'method' => 'POST',
            'url' => 'item_prices',
            'title' => trans('texts.new_item_price'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'saleTypePublicId' => Input::old('saleType') ? Input::old('saleType') : $request->sale_type_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_prices.edit', $data);
    }

    public function store(CreateItemPriceRequest $request)
    {
        $data = $request->input();

        $itemPrice = $this->itemPriceService->save($data);

        return redirect()->to("item_prices/{$itemPrice->public_id}/edit")->with('success', trans('texts.created_item_price'));
    }

    public function edit(ItemPriceRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_PRICE);
        $itemPrice = $request->entity();
        if ($clone) {
            $itemPrice->id = null;
            $itemPrice->public_id = null;
            $itemPrice->deleted_at = null;
            $method = 'POST';
            $url = 'item_prices';
        } else {
            $method = 'PUT';
            $url = 'item_prices/' . $itemPrice->public_id;
        }
        $data = [
            'product' => null,
            'saleType' => null,
            'itemPrice' => $itemPrice,
            'entity' => $itemPrice,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_price'),
            'productPublicId' => $itemPrice->product ? $itemPrice->product->public_id : null,
            'saleTypePublicId' => $itemPrice->saleType ? $itemPrice->saleType->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemPrice));

        return View::make('item_prices.edit', $data);
    }

    public function update(UpdateItemPriceRequest $request)
    {
        $data = $request->input();

        $itemPrice = $this->itemPriceService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_prices/%s/clone', $itemPrice->public_id))->with('success', trans('texts.clone_item_price'));
        } else {
            return redirect()->to("item_prices/{$itemPrice->public_id}/edit")->with('success', trans('texts.updated_item_price'));
        }
    }

    public function cloneItemPrice(ItemPriceRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemPriceService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_price', $count);

        return $this->returnBulk(ENTITY_ITEM_PRICE, $action, $ids)->with('success', $message);
    }

    private static function getViewModel($itemPrice = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::withCategory('itemBrand.itemCategory'),
            'saleTypes' => SaleType::scope()->withActiveOrSelected($itemPrice ? $itemPrice->sale_type_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_prices/{$publicId}/edit");
    }
}
