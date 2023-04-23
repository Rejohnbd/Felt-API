<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/vehicle-types",
     *     tags={"getAllVehicleTypes"},
     *     summary="Returns all vehicle type",
     *     description="",
     *     operationId="getAllVehicleTypes",
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
        $data = VehicleType::all();
        return Response([
            'status'    => true,
            'message'   => 'All Vehicle Type',
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
     *     path="/api/admin/vehicle-types",
     *     tags={"addVehicleTypes"},
     *     operationId="addVehicleTypes",
     *     @OA\Parameter(
     *         name="vehicle_type_name",
     *         in="path",
     *         description="Vehicle Type Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_type_image",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="file",
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request): Response
    {
        $request->request->add(['vehicle_type_slug' => Str::slug($request->vehicle_type_name)]);
        $validator = validator(
            $request->all(),
            [
                'vehicle_type_name'     => 'required|string',
                'vehicle_type_slug'     => 'required|string|unique:vehicle_types,vehicle_type_slug',
                'vehicle_type_image'    => 'required|mimes:png'
            ],
            [
                'vehicle_type_name.required'    => 'Vehicle Type Name is Required',
                'vehicle_type_name.string'      => 'Provide Valid Vehicle Type Name',
                'vehicle_type_slug.unique'      => 'This Vehicle Type Already Exist',
                'vehicle_type_image.required'   => 'Vehicle Type Image is Required.',
                'vehicle_type_image.mimes'      => 'Only PNG format Image Accepted.',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $newVehicleType = new VehicleType;
            $file = $request->file('vehicle_type_image');
            $fileExtension = $request->vehicle_type_image->extension();
            $fileName = $request->vehicle_type_slug . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
            $folderpath = public_path() . '/vehicle_type_image';
            $file->move($folderpath, $fileName);

            $newVehicleType->vehicle_type_name  = $request->vehicle_type_name;
            $newVehicleType->vehicle_type_slug  = $request->vehicle_type_slug;
            $newVehicleType->vehicle_type_image = '/vehicle_type_image/' . $fileName;
            $newVehicleType->save();

            return Response([
                'status' => true,
                'message' => 'Vehicle Type Created Successfully.',
                'data' => $newVehicleType
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $vehicleInfo = VehicleType::find($id);
        if (!is_null($vehicleInfo)) :
            return Response([
                'status' => true,
                'message' => 'Vechile Type Info.',
                'data' => $vehicleInfo
            ], Response::HTTP_OK);
        else :
            return Response([
                'status' => false,
                'message' => 'Vechile Type Not Found.',
                'data' => $vehicleInfo
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
        // return Response(['data' => $request->all(), 'id' => $id]);
        $vehicleInfo = VehicleType::find($id);
        if (!is_null($vehicleInfo)) :
            $request->request->add(['vehicle_type_slug' => Str::slug($request->vehicle_type_name)]);
            $validator = validator(
                $request->all(),
                [
                    'vehicle_type_name'     => 'required|string',
                    'vehicle_type_slug'     => 'required|string|unique:vehicle_types,vehicle_type_slug,' . $vehicleInfo->id,
                    'vehicle_type_image'    => $request->hasFile('vehicle_type_image') ? 'required|mimes:png' : 'nullable'
                ],
                [
                    'vehicle_type_name.required'    => 'Vehicle Type Name is Required',
                    'vehicle_type_name.string'      => 'Provide Valid Vehicle Type Name',
                    'vehicle_type_slug.unique'      => 'This Vehicle Type Already Exist',
                    'vehicle_type_image.required'   => 'Vehicle Type Image is Required.',
                    'vehicle_type_image.mimes'      => 'Only PNG format Image Accepted.',
                ]
            );
            if ($validator->fails()) :
                return Response([
                    'status' => false,
                    'message' => $validator->getMessageBag()->first(),
                    'errors' => $validator->getMessageBag()
                ], Response::HTTP_BAD_REQUEST);
            else :

                if ($request->hasFile('vehicle_type_image')) :
                    $image_path = public_path($vehicleInfo->vehicle_type_image);
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }

                    $file = $request->file('vehicle_type_image');
                    $fileExtension = $request->vehicle_type_image->extension();
                    $fileName = $request->vehicle_type_slug . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/vehicle_type_image';
                    $file->move($folderpath, $fileName);

                    $vehicleInfo->vehicle_type_image = '/vehicle_type_image/' . $fileName;
                endif;

                $vehicleInfo->vehicle_type_name  = $request->vehicle_type_name;
                $vehicleInfo->vehicle_type_slug  = $request->vehicle_type_slug;
                $vehicleInfo->save();

                return Response([
                    'status'    => true,
                    'message'   => 'Vehicle Type Updated Successfully.',
                    'data'      => $vehicleInfo
                ], Response::HTTP_CREATED);

            endif;
        else :
            return Response([
                'status'    => false,
                'message'   => 'Vehicle Type Not Found.',
                'data'      => $vehicleInfo
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
