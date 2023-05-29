<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\VehicleDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerVehicleDocument extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
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
     *     path="/api/customer/vehicle-documents",
     *     summary="Store Vehicle Document",
     *     tags={"vehicle-documents"},
     *     description="Store Vehicle Document.",
     *     operationId="vehicle-documents",
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         description="Vehicle Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_paper_id[]",
     *         in="path",
     *         description="Vehicle Paper Id",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expire_date[]",
     *         in="path",
     *         description="Expire Paper Id",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="document_image[]",
     *         in="path",
     *         description="Document Image ",
     *         required=false,
     *         @OA\Schema(
     *             type="file"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid user supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function store(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'            => 'required|exists:vehicles,id',
                'vehicle_paper_id.*'    => 'required|exists:vehicle_papers,id',
                'expire_date.*'         => 'required|date_format:Y-m-d',
                'document_image.*'      => 'nullable|mimes:jpg,jpeg,png',
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Required',
                'vehicle_id.exists'         => 'Provide Valid Vehicle Info',
                'vehicle_paper_id.*.required' => 'Vehicle Paper is Required',
                'vehicle_paper_id.*.exists'   => 'Provide Valid Vehicle Paper',
                'expire_date.*.required'      => 'Expire Date is Required',
                'expire_date.*.date_format'   => 'Provide Valid Date Format:YYYY-MM-DD',
                'document_image.*.mimes'      => 'Document Image must be .jpg, .jpeg, .png format',
            ]
        );

        $uniqueArray = array_unique($request->vehicle_paper_id);

        $validator->after(function ($validator) use ($uniqueArray, $request) {
            if (sizeof($uniqueArray) != sizeof($request->vehicle_paper_id)) {
                $validator->errors()->add('vehicle_paper_id', 'Vehicle Paper Selection Conflicted');
            }
        });

        $validator->after(function ($validator) use ($request) {
            if (sizeof($request->expire_date) != sizeof($request->vehicle_paper_id)) {
                $validator->errors()->add('vehicle_paper_id', 'Vehicle Paper Selection & Expire Date Selection Not Same');
            }
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            DB::beginTransaction();
            try {
                $customerId = Auth::user()->id;
                for ($i = 0; $i < sizeof($uniqueArray); $i++) :
                    $newVehicleDocument = new VehicleDocument;
                    $newVehicleDocument->customer_id = $customerId;
                    $newVehicleDocument->vehicle_id = $request->vehicle_id;
                    $newVehicleDocument->vehicle_paper_id   = $request->vehicle_paper_id[$i];
                    $newVehicleDocument->expire_date        = $request->expire_date[$i];

                    $files = $request->file('document_image');
                    if (!is_null($files)) :
                        foreach ($files as $fileKey => $file) :
                            if ($i == $fileKey) :
                                $fileExtension = $file->extension();
                                $fileName =  $request->expire_date[$i] . "_" . Str::random(5) . "_" . $request->vehicle_paper_id[$i] .  "_" . date('his') . '.' . $fileExtension;
                                $folderpath = public_path() . '/vehicle_document';
                                $file->move($folderpath, $fileName);
                                $newVehicleDocument->document_image = '/vehicle_document/' . $fileName;
                            endif;
                        endforeach;
                    endif;

                    $newVehicleDocument->save();
                endfor;

                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'Vehicle Document Created Successfully',
                    'data'      => $newVehicleDocument
                ], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                DB::rollback();
                return Response([
                    'status'    => false,
                    'message'   => $e->getMessage(),
                    'errors'    => []
                ], Response::HTTP_BAD_REQUEST);
            }
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
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
    public function update(Request $request, string $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }
}
