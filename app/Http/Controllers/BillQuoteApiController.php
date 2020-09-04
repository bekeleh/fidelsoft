<?php

namespace App\Http\Controllers;

use App\Models\Bill;

class BillQuoteApiController extends BillApiController
{
    protected $billRepo;

    protected $entityType = ENTITY_BILL;

    /**
     * @SWG\Get(
     *   path="/quotes",
     *   summary="List quotes",
     *   operationId="listQuotes",
     *   tags={"quote"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of quotes",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $invoices = Bill::scope()->withTrashed()
            ->quotes()->with('invoice_items', 'vendor')
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($invoices);
    }
}
