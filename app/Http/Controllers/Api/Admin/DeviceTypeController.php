<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class DeviceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/device-types",
     *     tags={"getAllDeviceTypes"},
     *     summary="Returns all device type",
     *     description="",
     *     operationId="getAllDeviceTypes",
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
        $data = DeviceType::all();
        return Response(['status' => true, 'message' => 'All Device Type', 'data' => $data], Response::HTTP_OK);
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
     * Add a new pet to the store.
     *
     * @OA\Post(
     *     path="/api/device-types",
     *     tags={"addDeviceType"},
     *     operationId="addDeviceType",
     *     @OA\Parameter(
     *         name="device_type_name",
     *         in="path",
     *         description="Device Type Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="device_configure_text",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
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
        $request->request->add(['device_type_slug' => Str::slug($request->device_type_name)]);
        $validator = validator(
            $request->all(),
            [
                'device_type_name'      => 'required|string',
                'device_type_slug'      => 'required|string|unique:device_types,device_type_slug',
                'device_configure_text' => 'required|string'
            ],
            [
                'device_type_name.required'         => 'Device Type Name is Required',
                'device_type_name.string'           => 'Provide Valid Device Type Name',
                'device_type_slug.unique'           => 'This Device Type Already Exist',
                'device_configure_text.required'    => 'This Device Configuration Text is Required.',
                'device_configure_text.string'      => 'Provide Valid Device Configuration Text.',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $newDeviceType = new DeviceType;
            $newDeviceType->device_type_name        = $request->device_type_name;
            $newDeviceType->device_type_slug        = $request->device_type_slug;
            $newDeviceType->device_configure_text   = $request->device_configure_text;
            $newDeviceType->save();

            return Response([
                'status' => true,
                'message' => 'Device Type Created Successfully.',
                'data' => $newDeviceType
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $deviceInfo = DeviceType::find($id);
        if (!is_null($deviceInfo)) :
            return Response([
                'status' => true,
                'message' => 'Device Type Info.',
                'data' => $deviceInfo
            ], Response::HTTP_OK);
        else :
            return Response([
                'status' => false,
                'message' => 'Device Type Not Found.',
                'data' => $deviceInfo
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
        $deviceInfo = DeviceType::find($id);
        if (!is_null($deviceInfo)) :
            $request->request->add(['device_type_slug' => Str::slug($request->device_type_name)]);
            $validator = validator(
                $request->all(),
                [
                    'device_type_name'      => 'required|string',
                    'device_type_slug'      => 'required|string|unique:device_types,device_type_slug,' . $deviceInfo->id,
                    'device_configure_text' => 'required|string'
                ],
                [
                    'device_type_name.required'         => 'Device Type Name is Required',
                    'device_type_name.string'           => 'Provide Valid Device Type Name',
                    'device_type_slug.unique'           => 'This Device Type Already Exist',
                    'device_configure_text.required'    => 'This Device Configuration Text is Required.',
                    'device_configure_text.string'      => 'Provide Valid Device Configuration Text.',
                ]
            );
            if ($validator->fails()) :
                return Response([
                    'status' => false,
                    'message' => $validator->getMessageBag()->first(),
                    'errors' => $validator->getMessageBag()
                ], Response::HTTP_BAD_REQUEST);
            else :
                $deviceInfo->device_type_name        = $request->device_type_name;
                $deviceInfo->device_type_slug        = $request->device_type_slug;
                $deviceInfo->device_configure_text   = $request->device_configure_text;
                $deviceInfo->save();

                return Response([
                    'status' => true,
                    'message' => 'Device Type Updated Successfully.',
                    'data' => $deviceInfo
                ], Response::HTTP_CREATED);

            endif;
        else :
            return Response([
                'status' => false,
                'message' => 'Device Type Not Found.',
                'data' => $deviceInfo
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
