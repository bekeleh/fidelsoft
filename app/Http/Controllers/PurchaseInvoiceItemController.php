<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillItemRequest;
use App\Http\Requests\BillItemRequest;
use App\Http\Requests\UpdateBillItemRequest;
use App\Libraries\Utils;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Ninja\Datatables\InvoiceItemDatatable;
use App\Ninja\Repositories\BillItemRepository;
use App\Services\BillItemService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class BillItemController.
 */
class BillItemController extends BaseController
{
    protected $BillItemRepo;
    protected $BillItemService;
    protected $entityType = ENTITY_BILL_ITEM;

    public function __construct(BillItemRepository $BillItemRepo, BillItemService $BillItemService)
    {
        // parent::__construct();

        $this->BillItemRepo = $BillItemRepo;
        $this->BillItemService = $BillItemService;
    }


    public function index()
    {
        $this->authorize('view', ENTITY_BILL_ITEM);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_BILL_ITEM,
            'datatable' => new InvoiceItemDatatable(),
            'title' => trans('texts.Bill_items'),
        ]);
    }

    public function getDatatable($invoiceItemPublicId = null)
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->BillItemService->getDatatable($account, $search);
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->BillItemService->getDatatableProduct($productPublicId);
    }

    public function create(BillItemRequest $request)
    {
        $this->authorize('create', ENTITY_BILL_ITEM);
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }

        $data = [
            'product' => $product,
            'invoiceItem' => null,
            'method' => 'POST',
            'url' => 'Bill_items',
            'title' => trans('texts.new_invoice_item'),
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('Bill_items.edit', $data);
    }

    private static function getViewModel($invoiceItem = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'products' => Product::scope()->withActiveOrSelected($invoiceItem ? $invoiceItem->product_id : false)->orderBy('product_key')->get(),
        ];
    }

    public function store(CreateBillItemRequest $request)
    {
        $data = $request->input();
        $invoiceItem = $request->entity();
        $invoiceItem = $this->BillItemService->save($data, $invoiceItem);

        if ($invoiceItem) {
            return redirect()->to("Bill_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.created_invoice_item'));
        }

        return redirect()->to("Bill_items");
    }

    public function edit(BillItemRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_BILL_ITEM);
        $invoiceItem = InvoiceItem::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $invoiceItem->id = null;
            $invoiceItem->public_id = null;
            $invoiceItem->deleted_at = null;
            $method = 'POST';
            $url = 'Bill_items';
        } else {
            $method = 'PUT';
            $url = 'Bill_items/' . $invoiceItem->public_id;
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

        return View::make('Bill_items.edit', $data);
    }

    public function update(UpdateBillItemRequest $request)
    {
        $data = $request->input();

        $invoiceItem = $this->BillItemService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('Bill_items/%s/clone', $invoiceItem->public_id))->with('success', trans('texts.clone_invoice_item'));
        } else {
            return redirect()->to("Bill_items/{$invoiceItem->public_id}/edit")->with('success', trans('texts.updated_invoice_item'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->BillItemService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_invoice_item', $count);

        return $this->returnBulk(ENTITY_BILL_ITEM, $action, $ids)->with('message', $message);
    }

    public function cloneInvoiceItem(BillItemRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("Bill_items/{$publicId}/edit");
    }
}
