<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;

class PurchaseQuoteApiController extends PurchaseInvoiceApiController
{
    protected $purchaseInvoiceRepo;

    protected $entityType = ENTITY_PURCHASE_INVOICE;

    /**
     * @SWG\Get(
     *   path="/quotes",
     *   summary="List quotes",
     *   operationId="listQuotes",
     *   tags={"quote"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of quotes",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/PurchaseInvoice"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $invoices = PurchaseInvoice::scope()
            ->withTrashed()
            ->quotes()
            ->with('purchase_invoice_items', 'vendor')
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($invoices);
    }
}
