<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseInvoiceItemRequest;
use App\Http\Requests\PurchaseInvoiceItemRequest;
use App\Http\Requests\UpdatePurchaseInvoiceItemRequest;
use App\Libraries\Utils;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\PurchaseInvoiceItemRepository;
use App\Services\PurchaseInvoiceItemService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class PurchaseInvoiceItemController.
 */
class PurchaseInvoiceItemController extends BaseController
{
    protected $purchaseInvoiceItemRepo;
    protected $purchaseInvoiceItemService;
    protected $entityType = ENTITY_PURCHASE_INVOICE_ITEM;

    public function __construct(PurchaseInvoiceItemRepository $purchaseInvoiceItemRepo, PurchaseInvoiceItemService $purchaseInvoiceItemService)
    {
        // parent::__construct();

        $this->purchaseInvoiceItemRepo = $purchaseInvoiceItemRepo;
        $this->purchaseInvoiceItemService = $purchaseInvoiceItemService;
    }


    public function index()
    {
        $this->authorize('view', ENTITY_PURCHASE_INVOICE_ITEM);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PURCHASE_INVOICE_ITEM,
            'datatable' => new InvoiceItemDatatable(),
            'title' => trans('texts.purchase_invoice_items'),
        ]);
    }

    public function getDatatable($invoiceItemPublicId = null)
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->purchaseInvoiceItemService->getDatatable($account, $search);
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->purchaseInvoiceItemService->getDatatableProduct($productPublicId);
    }

    public function create(PurchaseInvoiceItemRequest $request)
    {
        $this->authorize('create', ENTITY_PURCHASE_INVOICE_ITEM);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }

        $data = [
            'product' => $product,
            'invoiceItem' => null,
            'method' => 'POST',
            'url' => 'purchase_invoice_items',
            'title' => trans('texts.new_invoice_item'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('purchase_invoice_items.edit', $data);
    }

    private static function getViewModel($invoiceItem = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected($invoiceItem ? $invoiceItem->product_id : false)->orderBy('product_key')->get(),
        ];
    }

    public function store(CreatePurchaseInvoiceItemRequest $request)
    {
        $data = $request->input();
        $invoiceItem = $request->entity();
        $invoiceItem = $this->purchaseInvoiceItemService->save($data, $invoiceItem);

        if ($invoiceItem) {
            return redirect()->to("purchase_invoice_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.created_invoice_item'));
        }

        return redirect()->to("purchase_invoice_items");
    }

    public function edit(PurchaseInvoiceItemRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_PURCHASE_INVOICE_ITEM);
        $invoiceItem = InvoiceItem::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $invoiceItem->id = null;
            $invoiceItem->public_id = null;
            $invoiceItem->deleted_at = null;
            $method = 'POST';
            $url = 'purchase_invoice_items';
        } else {
            $method = 'PUT';
            $url = 'purchase_invoice_items/' . $invoiceItem->public_id;
        }

        $data = [
            'product' => null,
            'store' => null,
            'invoiceItem' => $invoiceItem,
            'entity' => $invoiceItem,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_invoice_item'),
            'productPublicId' => $invoiceItem->product ? $invoiceItem->product->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($invoiceItem));

        return View::make('purchase_invoice_items.edit', $data);
    }

    public function update(UpdatePurchaseInvoiceItemRequest $request)
    {
        $data = $request->input();

        $invoiceItem = $this->purchaseInvoiceItemService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('purchase_invoice_items/%s/clone', $invoiceItem->public_id))->with('success', trans('texts.clone_invoice_item'));
        } else {
            return redirect()->to("purchase_invoice_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.updated_invoice_item'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->purchaseInvoiceItemService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_invoice_item', $count);

        return $this->returnBulk(ENTITY_PURCHASE_INVOICE_ITEM, $action, $ids)->with('message', $message);
    }

    public function cloneInvoiceItem(PurchaseInvoiceItemRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("purchase_invoice_items/{$publicId}/edit");
    }
}
