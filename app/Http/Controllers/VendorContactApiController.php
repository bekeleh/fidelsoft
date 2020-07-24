<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendorContactRequest;
use App\Http\Requests\DeleteVendorContactRequest;
use App\Http\Requests\UpdateVendorContactRequest;
use App\Http\Requests\VendorContactRequest;
use App\Libraries\Utils;
use App\Models\VendorContact;
use App\Ninja\Repositories\VendorContactRepository;
use Illuminate\Support\Facades\Response;

class VendorContactApiController extends BaseAPIController
{
    protected $vendorContactRepo;

    protected $entityType = ENTITY_VENDOR_CONTACT;

    public function __construct(VendorContactRepository $vendorContactRepo)
    {
        parent::__construct();

        $this->vendorContactRepo = $vendorContactRepo;
    }

    public function ping()
    {
        $headers = Utils::getApiHeaders();

        return Response::make('', 200, $headers);
    }

    /**
     * @SWG\Get(
     *   path="/vendors",
     *   summary="List vendors",
     *   operationId="listVendors",
     *   tags={"vendor"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of vendors",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Vendor"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $vendorContacts = VendorContact::scope()
            ->withTrashed()
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($vendorContacts);
    }

    /**
     * @SWG\Get(
     *   path="/vendors/{vendor_id}",
     *   summary="Retrieve a vendor",
     *   operationId="getVendor",
     *   tags={"client"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="vendor_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single vendor",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Vendor"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param VendorContactRequest $request
     * @return
     */
    public function show(VendorContactRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/vendors",
     *   summary="Create a vendor",
     *   operationId="createVendor",
     *   tags={"vendor"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="vendor",
     *     @SWG\Schema(ref="#/definitions/Vendor")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New vendor",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Vendor"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateVendorContactRequest $request
     * @return
     */
    public function store(CreateVendorContactRequest $request)
    {
        $vendorContact = $this->vendorContactRepo->save($request->input());

        $vendorContact = VendorContact::scope($vendorContact->public_id)
            ->with('country', 'vendor_contacts', 'industry', 'size', 'currency')
            ->first();

        return $this->itemResponse($vendorContact);
    }

    /**
     * @SWG\Put(
     *   path="/vendors/{vendor_id}",
     *   summary="Update a vendor",
     *   operationId="updateVendor",
     *   tags={"vendor"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="vendor_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="vendor",
     *     @SWG\Schema(ref="#/definitions/Vendor")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated vendor",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Vendor"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateVendorContactRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateVendorContactRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $vendorContact = $this->vendorContactRepo->save($data, $request->entity());

        $vendorContact->load(['vendor_contacts']);

        return $this->itemResponse($vendorContact);
    }

    /**
     * @SWG\Delete(
     *   path="/vendors/{vendor_id}",
     *   summary="Delete a vendor",
     *   operationId="deleteVendor",
     *   tags={"vendor"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="vendor_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted vendor",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Vendor"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param DeleteVendorContactRequest $request
     * @return
     */
    public function destroy(DeleteVendorContactRequest $request)
    {
        $vendorContact = $request->entity();

        $this->vendorContactRepo->delete($vendorContact);

        return $this->itemResponse($vendorContact);
    }
}
