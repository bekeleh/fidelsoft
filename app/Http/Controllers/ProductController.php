<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Libraries\Utils;
use App\Models\ItemBrand;
use App\Models\Product;
use App\Models\TaxRate;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ProductController.
 */
class ProductController extends BaseController
{

    protected $productService;
    protected $productRepo;

    /**
     * ProductController constructor.
     *
     * @param ProductService $productService
     * @param ProductRepository $productRepo
     */
    public function __construct(ProductService $productService, ProductRepository $productRepo)
    {
        //parent::__construct();
        $this->productService = $productService;
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_PRODUCT);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PRODUCT,
            'datatable' => new ProductDatatable(),
            'title' => trans('texts.products'),
            'statuses' => Product::getStatuses(),
        ]);
    }

    public function getDatatable()
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->productService->getDatatable($account, $search);
    }

    public function getDatatableItemBrand($itemBrandPublicId = null)
    {
        return $this->productService->getDatatableItemBrand($itemBrandPublicId);
    }

//    public function getDatatableUnit($unitPublicId = null)
//    {
//        return $this->productService->getDatatableUnit($unitPublicId);
//    }

    public function create(ProductRequest $request)
    {
        $this->authorize('create', ENTITY_PRODUCT);
        $account = Auth::user()->account;
        if ($request->item_brand_id != 0) {
            $itemBrand = ItemBrand::scope($request->item_brand_id)->firstOrFail();
        } else {
            $itemBrand = null;
        }

//        if ($request->unit_id != 0) {
//            $unit = Unit::scope($request->unit_id)->firstOrFail();
//        } else {
//            $unit = null;
//        }

        $data = [
            'product' => null,
            'itemBrand' => $itemBrand,
//            'unit' => $unit,
            'method' => 'POST',
            'url' => 'products',
            'title' => trans('texts.create_product'),
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get(['id', 'name', 'rate']) : null,
            'itemBrandPublicId' => Input::old('itemBrand') ? Input::old('itemBrand') : $request->item_brand_id,
//            'unitPublicId' => Input::old('unit') ? Input::old('unit') : $request->unit_id,
        ];
        $data = array_merge($data, self::getViewModel());

        return View::make('products.edit', $data);
    }

    public function store(CreateProductRequest $request)
    {
        $data = $request->input();

        $product = $this->productService->save($data);

        return redirect()->to("products/{$product->public_id}/edit")->with('success', trans('texts.created_product'));
    }

    public function edit(ProductRequest $request, $publicId, $clone = false)
    {
        $this->authorize('edit', ENTITY_PRODUCT);
        Auth::user()->can('view', [ENTITY_PRODUCT, $request->entity()]);
        $account = Auth::user()->account;
        $product = Product::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $product->id = null;
            $product->public_id = null;
            $product->deleted_at = null;
            $url = 'products';
            $method = 'POST';
        } else {
            $url = 'products/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'itemBrand' => null,
//            'unit' => null,
            'product' => $product,
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get() : null,
            'entity' => $product,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_product'),
            'itemBrandPublicId' => $product->itemBrand ? $product->itemBrand->public_id : null,
//            'unitPublicId' => $product->unit ? $product->unit->public_id : null,

        ];
        $data = array_merge($data, self::getViewModel($product));

        return View::make('products.edit', $data);
    }

    public function update(UpdateProductRequest $request)
    {
        $data = $request->input();

        $product = $this->productService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('products/%s/clone', $product->public_id))->with('success', trans('texts.clone_product'));
        } else {
            return redirect()->to("products/{$product->public_id}/edit")->with('success', trans('texts.updated_product'));
        }
    }

    private static function getViewModel($product = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
//            'itemBrands' => ItemBrand::withCategory('itemCategory'),
//            'units' => Unit::scope()->withActiveOrSelected($product ? $product->unit_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("products/$publicId/edit");
    }

    public function cloneProduct(ProductRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $products = Product::scope($ids)->get();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->product_key;
            }
            return redirect("invoices/create")->with('selectedProducts', $data);
        } else {
            $count = $this->productService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_product', $count);

        return $this->returnBulk(ENTITY_PRODUCT, $action, $ids)->with('message', $message);
    }
}
