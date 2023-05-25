<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Response([
            'status'    => true,
            'message'   => 'Customer Dashboard',
            'data'      => 'ok'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/profile",
     *     tags={"getCustomerProfile"},
     *     summary="Returns Customer Profile",
     *     description="Returns Customer Profile Information",
     *     operationId="getCustomerProfile",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function getProfile()
    {
        $userInfo = Auth::user()->load(['userRole', 'userDetails']);
        $data = $this->formatCustomerInfo($userInfo);
        return Response([
            'status'    => true,
            'message'   => 'Customer Details',
            'data'      => $data
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/profile",
     *     summary="Updated Customer",
     *     tags={"updateCustomerProfile"},
     *     description="Update Customer User Profile.",
     *     operationId="updateCustomerProfile",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="path",
     *         description="First Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="path",
     *         description="Last Name",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_optional",
     *         in="path",
     *         description="Optional Phone",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email_optional",
     *         in="path",
     *         description="Optional Email",
     *         @OA\Schema(
     *             type="email"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         description="Address",
     *         @OA\Schema(
     *             type="string"
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
    public function updateProfile(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'first_name'        => 'required',
                'email_optional'    => 'nullable|email|max:255',
                'phone_optional'    => 'nullable|min:11|max:11',
                'address'           => $request->user_role == 4 ? 'required' : 'nullable',
                'image'             => 'nullable|mimes:jpeg,jpg,png'
            ],
            [
                'first_name.required'   => 'User First Name is Required',
                'email_optional.email'  => 'Provide Valid Optional Email',
                'email_optional.max'    => 'Provide Valid Optional Email',
                'phone_optional.min'    => 'Optional Phone Number not Less than 11',
                'phone_optional.max'    => 'Optional Phone Number not Greater than 11',
                'address.required'      => 'User Address Required',
                'image.mimes'           => 'User Image only jpg, jpeg & png format',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            DB::beginTransaction();

            try {
                $userInfo = Auth::user()->load(['userRole', 'userDetails']);

                $userInfo->userDetails->first_name      = $request->first_name;
                $userInfo->userDetails->last_name       = $request->last_name;
                $userInfo->userDetails->email_optional  = $request->email_optional;
                $userInfo->userDetails->phone_optional  = $request->phone_optional;
                $userInfo->userDetails->address         = $request->address;

                if ($request->hasFile('image')) :
                    if ($userInfo->userDetails->image != 'default.png') :
                        $image_path = public_path($userInfo->userDetails->image);
                        if (file_exists($image_path)) :
                            unlink($image_path);
                        endif;
                    endif;

                    $file = $request->file('image');
                    $fileExtension = $request->image->extension();
                    $fileName = $userInfo->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/user_image';
                    $file->move($folderpath, $fileName);
                    $userInfo->userDetails->image = '/user_image/' . $fileName;
                endif;

                $userInfo->userDetails->save();
                $data = $this->formatCustomerInfo($userInfo);
                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'Profile Update Successfully',
                    'data'      => $data
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
     * @OA\Post(
     *     path="/api/customer/update-password",
     *     summary="Updated Customer",
     *     tags={"updateCustomerPassword"},
     *     description="Update Customer User Profile.",
     *     operationId="updateCustomerPassword",
     *     @OA\Parameter(
     *         name="old_password",
     *         in="path",
     *         description="Old Password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="new_password",
     *         in="path",
     *         description="New Password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="confirm_new_password",
     *         in="path",
     *         description="Confirm New Password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
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
    public function updatePassword(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'old_password'          => 'required',
                'new_password'          => 'required|string|min:8',
                'confirm_new_password'  => 'required|same:new_password',
            ],
            [
                'old_password.required'         => 'Old Password is Required',
                'new_password.required'         => 'New Password is Required',
                'new_password.min'              => 'Password minimum length is 8',
                'confirm_new_password.required' => 'Confirm Password is Required',
                'confirm_new_password.same'     => 'New Password & Confirm Password not Match',

            ]
        );
        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->old_password, Auth::user()->password)) {
                $validator->errors()->add('old_password', 'Old passowrd is not correct');
            }
        });
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();

            return Response([
                'status'    => true,
                'message'   => 'Password Update Successfully',
                'data'      => []
            ], Response::HTTP_CREATED);
        endif;
    }

    private function formatCustomerInfo($userInfo)
    {
        $data = array(
            'id'                => $userInfo->id,
            'email'             => $userInfo->email,
            'email_optional'    => $userInfo->userDetails->email_optional,
            'first_name'        => $userInfo->userDetails->first_name,
            'last_name'         => $userInfo->userDetails->last_name,
            'phone_number'      => $userInfo->userDetails->phone_number,
            'phone_optional'    => $userInfo->userDetails->phone_optional,
            'address'           => $userInfo->userDetails->address,
            'image'             => $userInfo->userDetails->image,
            'user_status'       => $userInfo->user_status,
            'created_at'        => $userInfo->created_at,
        );
        return $data;
    }
}
