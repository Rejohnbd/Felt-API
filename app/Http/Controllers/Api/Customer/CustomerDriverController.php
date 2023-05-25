<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Driver\SingleDriverResouce;
use App\Http\Resources\CustomerDriverResource;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/customer/customer-drivers",
     *     tags={"customer-drivers"},
     *     summary="Returns all customer driver",
     *     description="",
     *     operationId="customer-drivers",
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
        $data = User::with('userDetails.vehicle')
            ->where('role_id', 5)
            ->where('created_by', Auth::user()->id)->get();

        return Response([
            'status'    => true,
            'message'   => 'Customer Driver',
            'data'      => CustomerDriverResource::collection($data)
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
     *     path="/api/customer/customer-drivers",
     *     summary="Store Driver Info",
     *     tags={"customer-drivers-add"},
     *     description="Store Driver.",
     *     operationId="customer-drivers-add",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="Driver Email",
     *         required=true,
     *         @OA\Schema(
     *             type="email"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="path",
     *         description="Driver Password",
     *         required=true,
     *         @OA\Schema(
     *             type="password"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="path",
     *         description="Driver Phone Number",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_optional",
     *         in="path",
     *         description="Driver Optional Phone Number",
     *         required=false,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="path",
     *         description="Driver First Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="path",
     *         description="Driver Last Name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="license_number",
     *         in="path",
     *         description="Driver License Number",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="license_expire_date",
     *         in="path",
     *         description="Driver License Expire Date",
     *         required=false,
     *         @OA\Schema(
     *             type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="driver_status",
     *         in="path",
     *         description="Driver Status in 0 or 1",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="license_picture",
     *         in="path",
     *         description="License Image",
     *         required=false,
     *         @OA\Schema(
     *             type="file"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="image",
     *         in="path",
     *         description="Driver Profile Image",
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
                // 'vehicle_id'            => 'required|numeric|exists:vehicles,id',
                'email'                 => 'required|email:rfc,dns|max:255|unique:users,email',
                'password'              => 'required|string|min:8',
                'phone_number'          => 'required|min:11|max:11|unique:users,phone_number',
                'phone_optional'        => 'nullable|min:11|max:11',
                'first_name'            => 'required|string',
                'license_number'        => 'required|unique:user_details,license_number',
                'license_expire_date'   => 'nullable|date_format:Y-m-d',
                'license_picture'       => 'nullable|mimes:jpeg,jpg,png',
                'image'                 => 'nullable|mimes:jpeg,jpg,png'
            ],
            [
                // 'vehicle_id.required'               => 'Vehicle Select Require',
                // 'vehicle_id.numeric'                => 'Provide Valid Vehicle Info',
                // 'vehicle_id.exists'                 => 'Provide Valid Vehicle Info',
                'email.required'                    => 'Email is Require',
                'email.email'                       => 'Provide Valid Email Address',
                'email.max'                         => 'Provide Valid Email Address',
                'email.unique'                      => 'Email Already Exist',
                'password.required'                 => 'User Password required',
                'password.min'                      => 'User Password not less than 8',
                'phone_number.required'             => 'Phone Number is Require',
                'phone_number.min'                  => 'Phone Number not less than 11 digits',
                'phone_number.max'                  => 'Phone Number not greater than 11 digits',
                'phone_number.unique'               => 'Phone Number Already Exists',
                'phone_optional.min'                => 'Optional Phone Number not less than 11 digits',
                'phone_optional.max'                => 'Optional Phone Number not greater than 11 digits',
                'first_name.required'               => 'First Name is Require',
                'first_name.string'                 => 'Provide Valid First Name',
                'license_number.required'           => 'License Number is Require',
                'license_number.unique'             => 'License Number already Exists',
                'license_expire_date.date_format'   => 'Provide Valid License Expire Date',
                'license_picture.mimes'             => 'License Picture Must be format .jpg, .jpeg, .png',
                'image.mimes'                       => 'Driver Picture Must be format .jpg, .jpeg, .png',
            ]
        );

        // $validator->after(function ($validator) use ($request) {
        //     $exists = Vehicle::where('id', $request->vehicle_id)->where('customer_id', Auth::user()->id)->exists();
        //     if (!$exists) :
        //         $validator->errors()->add('vehicle_id', 'Vehicle Info & Customer Info not Match');
        //     endif;
        // });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            DB::beginTransaction();
            try {
                DB::commit();

                $newUser = new User;
                $newUser->role_id       = 5;
                $newUser->email         = $request->email;
                $newUser->phone_number  = $request->phone_number;
                $newUser->password      = $request->password;
                $newUser->user_status   = $request->driver_status;
                $newUser->created_by    = Auth::user()->id;
                $newUser->save();

                $newUserDetails = new UserDetails;
                $newUserDetails->user_id                = $newUser->id;
                $newUserDetails->first_name             = $request->first_name;
                $newUserDetails->last_name              = $request->last_name;
                $newUserDetails->phone_optional         = $request->phone_optional;
                $newUserDetails->license_number         = $request->license_number;
                $newUserDetails->license_expire_date    = $request->license_expire_date;

                if ($request->hasFile('license_picture')) :
                    $file = $request->file('license_picture');
                    $fileExtension = $request->license_picture->extension();
                    $fileName = $newUser->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/license';
                    $file->move($folderpath, $fileName);
                    $newUserDetails->license_picture = '/license/' . $fileName;
                endif;

                if ($request->hasFile('image')) :
                    $file = $request->file('image');
                    $fileExtension = $request->image->extension();
                    $fileName = $newUser->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/user_image';
                    $file->move($folderpath, $fileName);
                    $newUserDetails->image = '/user_image/' . $fileName;
                endif;

                $newUserDetails->save();

                // Vehicle::where('id', $request->vehicle_id)
                //     ->where('customer_id', Auth::user()->id)
                //     ->update([
                //         'driver_id' => $newUser->id
                //     ]);

                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'Driver Created Successfully',
                    'data'      => $newUser
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
    /**
     * @OA\Get(
     *     path="/api/customer/customer-drivers/id",
     *     tags={"customer-driver-single"},
     *     summary="Get customer single driver ",
     *     description="",
     *     operationId="customer-driver-single",
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
    public function show(string $id): Response
    {
        $data = User::with('userDetails.vehicle')
            ->where('role_id', 5)
            ->where('created_by', Auth::user()->id)
            ->where('id', $id)->first();

        return Response([
            'status'    => true,
            'message'   => 'Customer Driver Details',
            'data'      => new SingleDriverResouce($data)
        ], Response::HTTP_OK);
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
