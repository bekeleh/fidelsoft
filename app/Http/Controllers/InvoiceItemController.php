<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceItemRequest;
use App\Http\Requests\InvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;
use App\Libraries\Utils;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\InvoiceItemRepository;
use App\Services\InvoiceItemService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class InvoiceItemController.
 */
class InvoiceItemController extends BaseController
{
    protected $invoiceItemRepo;
    protected $invoiceItemService;
    protected $entityType = ENTITY_INVOICE_ITEM;

    public function __construct(InvoiceItemRepository $invoiceItemRepo, InvoiceItemService $invoiceItemService)
    {
        // parent::__construct();

        $this->invoiceItemRepo = $invoiceItemRepo;
        $this->invoiceItemService = $invoiceItemService;
    }


    public function index()
    {
        $this->authorize('view', ENTITY_INVOICE_ITEM);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_INVOICE_ITEM,
            'datatable' => new InvoiceItemDatatable(),
            'title' => trans('texts.invoice_items'),
        ]);
    }

    public function getDatatable($invoiceItemPublicId = null)
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->invoiceItemService->getDatatable($account, $search);
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->invoiceItemService->getDatatableProduct($productPublicId);
    }

    public function create(InvoiceItemRequest $request)
    {
        $this->authorize('create', ENTITY_INVOICE_ITEM);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }

        $data = [
            'product' => $product,
            'invoiceItem' => null,
            'method' => 'POST',
            'url' => 'invoice_items',
            'title' => trans('texts.new_invoice_item'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('invoice_items.edit', $data);
    }

    private static function getViewModel($invoiceItem = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected($invoiceItem ? $invoiceItem->product_id : false)->orderBy('product_key')->get(),
        ];
    }

    public function store(CreateInvoiceItemRequest $request)
    {
        $data = $request->input();
        $invoiceItem = $request->entity();
        $invoiceItem = $this->invoiceItemService->save($data,$invoiceItem);

        if ($invoiceItem) {
            return redirect()->to("invoice_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.created_invoice_item'));
        }

        return redirect()->to("invoice_items");
    }

    public function edit(InvoiceItemRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_INVOICE_ITEM);
        $invoiceItem = InvoiceItem::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $invoiceItem->id = null;
            $invoiceItem->public_id = null;
            $invoiceItem->deleted_at = null;
            $method = 'POST';
            $url = 'invoice_items';
        } else {
            $method = 'PUT';
            $url = 'invoice_items/' . $invoiceItem->public_id;
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

        return View::make('invoice_items.edit', $data);
    }

    public function update(UpdateInvoiceItemRequest $request)
    {
        $data = $request->input();

        $invoiceItem = $this->invoiceItemService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('invoice_items/%s/clone', $invoiceItem->public_id))->with('success', trans('texts.clone_invoice_item'));
        } else {
            return redirect()->to("invoice_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.updated_invoice_item'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->invoiceItemService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_invoice_item', $count);

        return $this->returnBulk(ENTITY_INVOICE_ITEM, $action, $ids)->with('message', $message);
    }

    public function cloneInvoiceItem(InvoiceItemRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("invoice_items/{$publicId}/edit");
    }
}
