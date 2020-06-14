<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePointOfSaleRequest;
use App\Http\Requests\PointOfSaleRequest;
use App\Http\Requests\UpdatePointOfSaleRequest;
use App\Models\Product;
use App\Ninja\Repositories\ProductRepository;
use Illuminate\Http\Request;

class PointOfSaleApiController extends BaseAPIController
{
    protected $entityType = ENTITY_PRODUCT;

    protected $productRepo;

    /**
     * ProductApiController constructor.
     *
     * @param ProductRepository $productRepo
     */
    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct();

        $this->productRepo = $productRepo;
    }

    /**
     * @SWG\Get(
     *   path="/pointofsale",
     *   summary="Get products by barcode",
     *   operationId="getProductByBarcode",
     *   tags={"pointofsale"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="barcode",
     *     type="string",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A list of products with the barcode",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param Request $request
     * @return
     */
    public function productsByBarcode(Request $request)
    {
        $products = Product::with('manufacturerProductDetails')->whereHas('manufacturerProductDetails', function ($query) use ($request) {
            $query->where('upc', '=', $request->get('barcode'));
        })->get();

        return $this->response($products);
    }

    /**
     * @SWG\Get(
     *   path="/products",
     *   summary="List products",
     *   operationId="listProducts",
     *   tags={"product"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of products",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $products = Product::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($products);
    }

    /**
     * @SWG\Get(
     *   path="/products/{product_id}",
     *   summary="Retrieve a product",
     *   operationId="getProduct",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="product_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single product",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PointOfSaleRequest $request
     * @return
     */
    public function show(PointOfSaleRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/products",
     *   summary="Create a product",
     *   operationId="createProduct",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Product")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New product",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreatePointOfSaleRequest $request
     * @return
     */

    public function store(CreatePointOfSaleRequest $request)
    {
        $product = $this->productRepo->save($request->input());

        return $this->itemResponse($product);
    }

    /**
     * @SWG\Put(
     *   path="/products/{product_id}",
     *   summary="Update a product",
     *   operationId="updateProduct",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="product_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     @SWG\Schema(ref="#/definitions/Product")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated product",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdatePointOfSaleRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdatePointOfSaleRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $product = $this->productRepo->save($data, $request->entity());

        return $this->itemResponse($product);
    }

    /**
     * @SWG\Delete(
     *   path="/products/{product_id}",
     *   summary="Delete a product",
     *   operationId="deleteProduct",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="product_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted product",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Product"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdatePointOfSaleRequest $request
     * @return
     */
    public function destroy(UpdatePointOfSaleRequest $request)
    {
        $product = $request->entity();

        $this->productRepo->delete($product);

        return $this->itemResponse($product);
    }
}
