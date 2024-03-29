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
use Illuminate\Support\Facades\Hash;
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
     *     security={{"sanctum": {} }},
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
                'email'                 => 'required|email:rfc,dns|max:255|unique:users,email',
                'password'              => 'required|string|min:8',
                'phone_number'          => 'required|min:11|max:11|unique:users,phone_number',
                'phone_optional'        => 'nullable|min:11|max:11',
                'first_name'            => 'required|string',
                'license_number'        => 'required|unique:user_details,license_number',
                'license_expire_date'   => 'nullable|date_format:Y-m-d',
                'license_picture'       => 'nullable|mimes:jpeg,jpg,png',
                'image'                 => 'nullable|mimes:jpeg,jpg,png',
                'driver_status'         => 'required|in:0,1'
            ],
            [
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
                'driver_status.required'            => 'Driver Status Required',
                'driver_status.in'                  => 'Provide Valid Driver Status',
            ]
        );

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
                $newUser->password      = Hash::make($request->password);
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
     *     security={{"sanctum": {} }},
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
    /**
     * @OA\Put(
     *     path="/api/customer/customer-drivers/{id}",
     *     summary="Store Driver Info",
     *     tags={"customer-drivers-update"},
     *     description="Update Driver.",
     *     operationId="customer-drivers-update",
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
     *         required=false,
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
    public function update(Request $request, string $id): Response
    {
        $data = User::with('userDetails.vehicle')
            ->where('role_id', 5)
            ->where('created_by', Auth::user()->id)
            ->findOrFail($id);

        $validator = validator(
            $request->all(),
            [
                'email'                 => 'nullable|email:rfc,dns|max:255|unique:users,email,' . $data->id,
                'password'              => 'nullable|string|min:8',
                'phone_number'          => 'required|min:11|max:11|unique:users,phone_number,' . $data->id,
                'phone_optional'        => 'nullable|min:11|max:11',
                'first_name'            => 'required|string',
                'license_number'        => 'required|unique:user_details,license_number,' . $data->id,
                'license_expire_date'   => 'nullable|date_format:Y-m-d',
                'license_picture'       => 'nullable|mimes:jpeg,jpg,png',
                'image'                 => 'nullable|mimes:jpeg,jpg,png',
                'driver_status'         => 'required|in:0,1'
            ],
            [
                'email.email'                       => 'Provide Valid email Address',
                'email.unique'                      => 'Email Address Already Exists',
                'password.min'                      => 'Password Minimum Length 8 digits',
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
                'driver_status.required'            => 'Driver Status Required',
                'driver_status.in'                  => 'Provide Valid Driver Status',
            ]
        );

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

                $data->email         = $request->email;
                $data->phone_number  = $request->phone_number;
                if ($request->has('password')) :
                    $data->password      = Hash::make($request->password);
                endif;
                $data->user_status   = $request->driver_status;
                $data->save();


                $data->userDetails->first_name             = $request->first_name;
                $data->userDetails->last_name              = $request->last_name;
                $data->userDetails->phone_optional        = $request->phone_optional;
                $data->userDetails->license_number        = $request->license_number;
                $data->userDetails->license_expire_date   = $request->license_expire_date;

                if ($request->hasFile('license_picture')) :
                    if (!is_null($data->userDetails->license_picture)) :
                        $image_path = public_path($data->userDetails->license_picture);
                        if (file_exists($image_path)) :
                            unlink($image_path);
                        endif;
                    endif;

                    $file = $request->file('license_picture');
                    $fileExtension = $request->license_picture->extension();
                    $fileName = $data->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/license';
                    $file->move($folderpath, $fileName);
                    $data->userDetails->license_picture = '/license/' . $fileName;
                endif;

                if ($request->hasFile('image')) :
                    if ($data->userDetails->image != 'default.png') :
                        $image_path = public_path($data->userDetails->image);
                        if (file_exists($image_path)) :
                            unlink($image_path);
                        endif;
                    endif;

                    $file = $request->file('image');
                    $fileExtension = $request->image->extension();
                    $fileName = $data->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/user_image';
                    $file->move($folderpath, $fileName);
                    $data->userDetails->image = '/user_image/' . $fileName;
                endif;

                $data->userDetails->save();

                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'Driver Update Successfully',
                    'data'      => new SingleDriverResouce($data)
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
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/customer/customer-drivers/id",
     *     tags={"customer-drivers-delete"},
     *     summary="Delete customer driver",
     *     description="",
     *     operationId="customer-drivers-delete",
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
    public function destroy(string $id): Response
    {
        $data = User::with('userDetails.vehicle')
            ->where('role_id', 5)
            ->where('created_by', Auth::user()->id)
            ->findOrFail($id);

        if (!is_null($data->userDetails->vehicle)) :
            $data->userDetails->vehicle->update([
                'driver_id' => Auth::user()->id
            ]);
        endif;

        $data->userDetails->delete();
        $data->delete();

        return Response([
            'status'    => true,
            'message'   => 'Customer Driver Deleted',
            'data'      => new SingleDriverResouce($data)
        ], Response::HTTP_OK);
    }
}
