<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Libraries\Utils;
use App\Models\ItemCategory;
use App\Models\Product;
use App\Models\TaxRate;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Exception;
use Redirect;

/**
 * Class ProductController.
 */
class ProductController extends BaseController
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var ProductRepository
     */
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

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PRODUCT,
            'datatable' => new ProductDatatable(),
            'title' => trans('texts.products'),
            'statuses' => Product::getStatuses(),
        ]);
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getDatatable()
    {
        return $this->productService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function getDatatableItemCategory($itemCategoryPublicId = null)
    {
        return $this->productService->getDatatableItemCategory($itemCategoryPublicId);
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(ProductRequest $request)
    {
        $account = Auth::user()->account;
        if ($request->category_id != 0) {
            $itemCategory = ItemCategory::scope($request->category_id)->firstOrFail();
        } else {
            $itemCategory = null;
        }

        $data = [
            'product' => null,
            'itemCategory' => $itemCategory,
            'method' => 'POST',
            'url' => 'products',
            'title' => trans('texts.create_product'),
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get(['id', 'name', 'rate']) : null,
            'itemCategoryPublicId' => Input::old('itemCategory') ? Input::old('itemCategory') : $request->category_id,
        ];
        $data = array_merge($data, self::getViewModel());

        return View::make('products.edit', $data);
    }

    /**
     * @param ProductRequest $request
     * @param $publicId
     *
     * @param bool $clone
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(ProductRequest $request, $publicId, $clone = false)
    {
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
            'itemCategory' => null,
            'product' => $product,
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get() : null,
            'entity' => $product,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_product'),
            'itemCategoryPublicId' => $product->itemCategory ? $product->itemCategory->public_id : null,
        ];
        $data = array_merge($data, self::getViewModel($product));

        return View::make('products.edit', $data);
    }


    /**
     * @param ProductRequest $request
     * @return RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        $data = $request->input();
        $product = $this->productService->save($data);

        Session::flash('message', trans('texts.created_product'));

        return redirect()->to("products/{$product->public_id}/edit");
    }

    /**
     * @param ProductRequest $request
     * @return RedirectResponse
     */
    public function update(ProductRequest $request)
    {
        $data = $request->input();

        $product = $this->productService->save($data, $request->entity());

        Session::flash('message', trans('texts.updated_product'));

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('products/%s/clone', $product->public_id));
        } else {
            return redirect()->to("products/{$product->public_id}/edit");
        }
    }

    private static function getViewModel($product = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'itemCategories' => ItemCategory::scope()->withActiveOrSelected($product ? $product->category_id : false)->orderBy('name')->get(),
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

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $products = Product::scope($ids)->get();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->name;
            }
            return redirect("invoices/create")->with('selectedProducts', $data);
        } else {
            $count = $this->productService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_product', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_PRODUCT, $action, $ids);
    }
}
