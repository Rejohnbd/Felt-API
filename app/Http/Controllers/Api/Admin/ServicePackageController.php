<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ServicePackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/service-packages",
     *     tags={"getAllPackages"},
     *     summary="Returns all device type",
     *     description="",
     *     operationId="getAllPackages",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\AdditionalProperties(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function index(): Response
    {
        $data = ServicePackage::all();
        return Response([
            'status'    => true,
            'message'   => 'All Packages',
            'data'      => $data
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/service-packages",
     *     tags={"createPackage"},
     *     operationId="createPackage",
     *     @OA\Parameter(
     *         name="package_name",
     *         in="path",
     *         description="Package Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="installation_charge",
     *         in="query",
     *         description="Installation Charge",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="subscription_fee",
     *         in="query",
     *         description="Subcription Fee",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="package_features",
     *         in="query",
     *         description="Package Features",
     *         required=false,
     *         @OA\Schema(
     *             type="array['value']",
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function store(Request $request): Response
    {
        $request->request->add(['package_name_slug' => Str::slug($request->package_name)]);
        $validator = validator(
            $request->all(),
            [
                'package_name'          => 'required|string',
                'package_name_slug'     => 'required|string|unique:service_packages,package_name_slug',
                'installation_charge'   => 'required|numeric|gt:0',
                'subscription_fee'      => 'required|numeric|gt:0'
            ],
            [
                'package_name.required'         => 'Package Name is Required',
                'package_name.string'           => 'Provide Valid Package Name',
                'package_name_slug.unique'      => 'This Package Name is Already Exist',
                'installation_charge.required'  => 'Installation Charge is Required.',
                'installation_charge.numeric'   => 'Provide Valid Installation Charge.',
                'installation_charge.gt'        => 'Provide Valid Installation Charge.',
                'subscription_fee.required'     => 'Subscription Fee is Required.',
                'subscription_fee.numeric'      => 'Provide Valid Subscription Fee.',
                'subscription_fee.gt'           => 'Provide Valid Subscription Fee.'
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $newServicePackage = new ServicePackage;
            $newServicePackage->package_name        = $request->package_name;
            $newServicePackage->package_name_slug   = $request->package_name_slug;
            $newServicePackage->installation_charge = $request->installation_charge;
            $newServicePackage->subscription_fee    = $request->subscription_fee;
            $newServicePackage->package_features    = $request->has('package_features') ? $request->package_features : null;
            $newServicePackage->save();

            return Response([
                'status'    => true,
                'message'   => 'Package Created Successfully.',
                'data'      => $newServicePackage
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $data = ServicePackage::find($id);
        if (!is_null($data)) :
            return Response([
                'status'    => true,
                'message'   => 'Package Info.',
                'data'      => $data
            ], Response::HTTP_OK);
        else :
            return Response([
                'status'    => false,
                'message'   => 'Package Not Found.',
                'data'      => $data
            ], Response::HTTP_NOT_FOUND);
        endif;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        $data = ServicePackage::find($id);
        if (!is_null($data)) :
            $request->request->add(['package_name_slug' => Str::slug($request->package_name)]);
            $validator = validator(
                $request->all(),
                [
                    'package_name'          => 'required|string',
                    'package_name_slug'     => 'required|string|unique:service_packages,package_name_slug,' . $data->id,
                    'installation_charge'   => 'required|numeric|gt:0',
                    'subscription_fee'      => 'required|numeric|gt:0'
                ],
                [
                    'package_name.required'         => 'Package Name is Required',
                    'package_name.string'           => 'Provide Valid Package Name',
                    'package_name_slug.unique'      => 'This Package Name is Already Exist',
                    'installation_charge.required'  => 'Installation Charge is Required.',
                    'installation_charge.numeric'   => 'Provide Valid Installation Charge.',
                    'installation_charge.gt'        => 'Provide Valid Installation Charge.',
                    'subscription_fee.required'     => 'Subscription Fee is Required.',
                    'subscription_fee.numeric'      => 'Provide Valid Subscription Fee.',
                    'subscription_fee.gt'           => 'Provide Valid Subscription Fee.'
                ]
            );
            if ($validator->fails()) :
                return Response([
                    'status'    => false,
                    'message'   => $validator->getMessageBag()->first(),
                    'errors'    => $validator->getMessageBag()
                ], Response::HTTP_BAD_REQUEST);
            else :

                $data->package_name        = $request->package_name;
                $data->package_name_slug   = $request->package_name_slug;
                $data->installation_charge = $request->installation_charge;
                $data->subscription_fee    = $request->subscription_fee;
                $data->package_features    = $request->has('package_features') ? $request->package_features : null;
                $data->save();

                return Response([
                    'status'    => true,
                    'message'   => 'Package Updated Successfully.',
                    'data'      => $data
                ], Response::HTTP_CREATED);

            endif;
        else :
            return Response([
                'status'    => false,
                'message'   => 'Package Not Found.',
                'data'      => $data
            ], Response::HTTP_NOT_FOUND);
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }
}
